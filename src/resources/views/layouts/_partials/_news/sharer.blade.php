<a href="http://twitter.com/share?text={{ $newsArticle->title }}&url={{ url('/news') }}/{{ $newsArticle->slug }}&hashtags={{ $newsArticle->getTags(',') }}" target="_blank" rel="noreferrer">
    <i class="fab fa-twitter"></i>
</a>