<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Joke;
use App\Models\Word;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('inspire')->everyTenMinutes();
        // $schedule->call(function () {
        //     \Log::info(__METHOD__, ['old_str count:']);
        //     \DB::table('jokes')->insert([
        //         ['content' => 'taylor@example.com'],
        //     ]);
        // })->everyMinute();
        // 查询 word 表中 importance = 4 的随机一条数据
        $word = Word::where("importance", ">", 2)->inRandomOrder()->first();
        $phonetic = $word->phonetic;
        // phonetic 拼接 🎙
        $phonetic = '🎙 ' . $phonetic;
        // content 拼接
        $content = $word->content;
        $explains = $word->explains;
        // 把 <br> 替换成换行符
        $explains = str_replace('<br>', "\n", $explains);
        $cmd_notify = sprintf("osascript -e 'display notification \"%s\" with title \"%s\" subtitle \"%s\"' && say \"%s\" && say \"%s\" && say \"%s\"", $explains, $content, $phonetic, $content, $content, $content);
        $schedule->exec($cmd_notify)->everyMinute();
        // 查询,
        // $schedule->command('route:list')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
