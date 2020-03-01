<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TopKeywords;
use DB;
use Storage;

class RefreshKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the feed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('top_keywords')->truncate();
        DB::table('articles')->truncate();

        $data = Storage::disk('local')->get('commonEngWords.json');
        $data = json_decode($data, true);
        $commonEngWords = [];

        foreach ($data as $item) {
            $commonEngWords[] = $item['text'];
        }

        $i = 0;

        $url = 'https://www.theregister.co.uk/software/headlines.atom';
        $xml = simplexml_load_file($url) or die("feed not loading");
        foreach($xml->entry as $entry) {
            $titleWords = explode(' ', $entry->title);
            
            DB::insert('insert into articles (updated, title, author, summary) values (?, ?, ?, ?)', [$entry->updated, $entry->title, $entry->author->name, $entry->summary]);
            foreach($titleWords as $word) {
                if (preg_match("/[a-z0-9]/", $word) && !preg_match("/\d|<|>/", $word)) {

                    $word = preg_replace("/('\w?)|,|\?|!|:|\.\.\./", '', $word);

                    foreach ($commonEngWords as $commWord) {
                        if(strtolower($word) === $commWord) {
                            continue 2;
                        }
                    }
                    $wordExists = DB::table('top_keywords')->whereIn('word',[strtolower($word)] )->first();

                    if (!empty($wordExists)) {
                        $pop = $wordExists->popularity;
                        $pop++;
                        DB::update('update top_keywords set popularity = ? where word = ?', [$pop, $word]);
                    }
                    else {
                        DB::insert('insert into top_keywords (word, popularity) values (?, ?)', [$word, 1]);
                    }
                }
            }
        }
    }
}
