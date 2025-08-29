<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupUnfinishedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:cleanup-orders {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up unfinished POS orders that are older than 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find orders that are in draft status and older than 1 hour
        $unfinishedOrders = Order::where('status', 'draft')
            ->where('created_at', '<', now()->subHour())
            ->get();

        if ($unfinishedOrders->isEmpty()) {
            $this->info('No unfinished orders found to clean up.');
            return 0;
        }

        $this->info("Found {$unfinishedOrders->count()} unfinished orders to clean up.");

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with cleaning up these orders?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $bar = $this->output->createProgressBar($unfinishedOrders->count());
        $bar->start();

        $deletedCount = 0;
        foreach ($unfinishedOrders as $order) {
            try {
                // Delete all order items first
                $order->items()->delete();
                // Delete the order
                $order->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Error deleting order {$order->id}: " . $e->getMessage());
                Log::error("Error deleting order {$order->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Successfully cleaned up {$deletedCount} unfinished orders.");
        Log::info("Manually cleaned up {$deletedCount} unfinished orders via artisan command");

        return 0;
    }
}
