<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Blog\Application\Services\Security\IPBlockServiceInterface;
use Illuminate\Console\Command;

class UnblockIPCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:unblock-ip
                            {ip : The IP address to unblock}
                            {--list : List all blocked IPs}
                            {--all : Unblock all IPs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unblock an IP address or manage IP blocks';

    public function __construct(
        private readonly IPBlockServiceInterface $ipBlockService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listBlockedIPs();
        }

        if ($this->option('all')) {
            return $this->unblockAllIPs();
        }

        $ip = $this->argument('ip');

        if (!$ip) {
            $this->error('IP address is required. Use --help for more information.');
            return Command::FAILURE;
        }

        return $this->unblockSingleIP($ip);
    }

    private function unblockSingleIP(string $ip): int
    {
        if (!$this->ipBlockService->isBlocked($ip)) {
            $this->info("IP {$ip} is not currently blocked.");
            return Command::SUCCESS;
        }

        $remainingTime = $this->ipBlockService->getRemainingBlockTime($ip);

        if ($this->ipBlockService->unblockIP($ip)) {
            $this->info("Successfully unblocked IP: {$ip}");
            $this->info("Remaining block time was: {$remainingTime} minutes");
            return Command::SUCCESS;
        }

        $this->error("Failed to unblock IP: {$ip}");
        return Command::FAILURE;
    }

    private function listBlockedIPs(): int
    {
        $blockedIPs = $this->ipBlockService->getBlockedIPs();

        if (empty($blockedIPs)) {
            $this->info('No IPs are currently blocked.');
            return Command::SUCCESS;
        }

        $this->info('Currently blocked IPs:');

        $headers = ['IP Address', 'Blocked At', 'Expires At', 'Reason', 'Remaining (min)'];
        $rows = [];

        foreach ($blockedIPs as $blockData) {
            $rows[] = [
                $blockData['ip'],
                $blockData['blocked_at']->format('Y-m-d H:i:s'),
                $blockData['expires_at']->format('Y-m-d H:i:s'),
                $blockData['reason'],
                $this->ipBlockService->getRemainingBlockTime($blockData['ip']),
            ];
        }

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }

    private function unblockAllIPs(): int
    {
        $blockedIPs = $this->ipBlockService->getBlockedIPs();

        if (empty($blockedIPs)) {
            $this->info('No IPs are currently blocked.');
            return Command::SUCCESS;
        }

        if (!$this->confirm('Are you sure you want to unblock ALL IPs? This action cannot be undone.')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $unblocked = 0;
        $failed = 0;

        foreach ($blockedIPs as $blockData) {
            if ($this->ipBlockService->unblockIP($blockData['ip'])) {
                $unblocked++;
                $this->info("Unblocked: {$blockData['ip']}");
            } else {
                $failed++;
                $this->error("Failed to unblock: {$blockData['ip']}");
            }
        }

        $this->info("Unblocked {$unblocked} IPs successfully.");

        if ($failed > 0) {
            $this->warn("Failed to unblock {$failed} IPs.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}