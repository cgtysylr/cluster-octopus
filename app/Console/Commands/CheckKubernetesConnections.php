<?php

namespace App\Console\Commands;

use App\Models\NetworkConnection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckKubernetesConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kubernetes:check-connections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Kubernetes node connectivity and store results in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nodeName = getenv('NODE_NAME') ?: 'unknown node';

        $nodeType = $this->getNodeType($nodeName);

        $workers = $this->getNodes("!node-role.kubernetes.io/control-plane,!node-role.kubernetes.io/master");
        $masters = $this->getNodes("node-role.kubernetes.io/control-plane,node-role.kubernetes.io/master");
        $allNodes = array_merge($workers, $masters);

        $controlList = [
            ["port" => 6443, "protocol" => "tcp", "source" => "WORKERS", "destination" => "MASTERS", "description" => "Kubernetes API"],
            ["port" => 9345, "protocol" => "tcp", "source" => "WORKERS", "destination" => "MASTERS", "description" => "RKE2 supervisor API"],
            ["port" => 10250, "protocol" => "tcp", "source" => "ALL_NODES", "destination" => "ALL_NODES", "description" => "kubelet metrics"],
            ["port" => 2379, "protocol" => "tcp", "source" => "MASTERS", "destination" => "MASTERS", "description" => "etcd client port"],
            ["port" => 2380, "protocol" => "tcp", "source" => "MASTERS", "destination" => "MASTERS", "description" => "etcd peer port"],
            ["port" => 8472, "protocol" => "udp", "source" => "ALL_NODES", "destination" => "ALL_NODES", "description" => "Canal CNI with VXLAN"]
        ];

        foreach ($controlList as $entry) {
            if ($entry['source'] === $nodeType || $entry['source'] === 'ALL_NODES') {
                $selectedArray = $this->getSelectedArray($entry['destination'], $workers, $masters, $allNodes);

                foreach ($selectedArray as $node) {
                    $destIp = $this->getNodeIP($node);

                    echo "Executing: nc -z -w 3 $destIp {$entry['port']} -- ";

                    $isAccessible = $this->checkPort($destIp, $entry['port']);

                    echo $isAccessible==1 ?: 0 ; echo "\n";

                    $this->updateDatabase($nodeName, $node, $entry['port'], $isAccessible, $entry['description']);
                }
            }
        }
    }

    private function getNodeType( $nodeName )
    {
        $output = shell_exec("kubectl get node $nodeName --show-labels");
        return str_contains($output, "node-role.kubernetes.io/control-plane=true") ? 'MASTERS' : 'WORKERS';
    }

    private function getNodes($selector)
    {
        $output = shell_exec("kubectl get nodes --selector='$selector' -o custom-columns='NAME:.metadata.name' --no-headers");
        return array_filter(explode("\n", trim($output)));
    }
    private function getSelectedArray($destination, $workers, $masters, $allNodes)
    {
        return match ($destination) {
            'WORKERS' => $workers,
            'MASTERS' => $masters,
            'ALL_NODES' => $allNodes,
            default => []
        };
    }

    private function getNodeIP($node)
    {
        return trim(shell_exec("kubectl get node $node -o=jsonpath='{.status.addresses[?(@.type==\"InternalIP\")].address}'"));
    }

    private function checkPort($ip, $port)
    {
        exec("nc -z -w 3 $ip $port 2>&1", $output, $returnCode);

        Log::info("Checking port connection", [
            'ip' => $ip,
            'port' => $port,
            'output' => implode(" ", $output),
            'return_code' => $returnCode
        ]);

        return $returnCode === 0;
    }


    private function updateDatabase($source, $destination, $port, $status, $description)
    {
        NetworkConnection::updateOrCreate(
            ['source' => $source, 'destination' => $destination, 'port' => $port],
            ['status' => $status ? 1 : 0, 'description' => $description]
        );
    }
}
