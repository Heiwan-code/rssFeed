@extends('layouts.app')

@section('pagespecificscripts')
<script src="{{ asset('js/loadRssFeed.js') }}" defer></script>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <h1 class="pageTitle">The Feed</h1>
                    <?php

                        $top10Keywords = DB::table('top_keywords')
                        ->orderBy('popularity', 'desc')
                        ->paginate(10);

                        foreach ($top10Keywords as $keyword) {
                            ?>
                            <a href="?keyword=<?= $keyword->word ?>" class="keyword-box">
                                <?= $keyword->word ?>
                            </a>
                            <?php
                        }
                    ?>
                    <div class="seperator"></div>
                    <?php

                        $articles = DB::table('articles')->get();

                        foreach ($articles as $article) {

                            if(isset($_GET['keyword'])) {
                                if (strpos($article->title, $_GET['keyword']) !== false) {
                                    echo '<div class="article-box">';
                                        echo '<h3 class="article-title">'.$article->title.'</h3>';
                                        echo $article->summary;
                                        echo '<div class="articles-footer">';
                                        echo '<p class="author">'.$article->author.'</p>';
                                        echo '<p class="date">'.preg_replace("/Z|T/", ' ', $article->updated).'</p>';
                                    echo '</div></div>';
                                }
                            }
                            else {
                                echo '<div class="article-box">';
                                        echo '<h3 class="article-title">'.$article->title.'</h3>';
                                        echo $article->summary;
                                        echo '<div class="articles-footer">';
                                        echo '<p class="author">'.$article->author.'</p>';
                                        echo '<p class="date">'.preg_replace("/Z|T/", ' ', $article->updated).'</p>';
                                    echo '</div></div>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
