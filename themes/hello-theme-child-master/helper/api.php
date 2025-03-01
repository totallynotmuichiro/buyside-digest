<?php

class BSD_API {

    public static function get_news() {
        $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/news-articles/", array('timeout' => 5000));
        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }

        $data = wp_remote_retrieve_body($response);
        $newsItems = json_decode($data, true);

        function has_all_required_news_data($newsItem)
        {
            return !empty($newsItem['source_name']) &&
                !empty($newsItem['title']) &&
                !empty($newsItem['link']) &&
                !empty($newsItem['published_date']) &&
                !empty($newsItem['excerpt']);
        }

        usort($newsItems, function ($a, $b) {
            $a_complete = has_all_required_news_data($a);
            $b_complete = has_all_required_news_data($b);

            if ($a_complete !== $b_complete) {
                return $b_complete - $a_complete; // Complete blogs come first
            }

            return strtotime($b['published_date']) - strtotime($a['published_date']);
        });

        $newsItems = array_slice($newsItems, 0, 8);

        return $newsItems;
    }

    public static function get_blogs() {
        // Fetch fresh data
        $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/blog-articles/", array( 'timeout' => 5000 ));
        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }
        
        $data = wp_remote_retrieve_body($response);
        $blogs = json_decode($data, true);
    
        // Function to check if a blog has all required fields
        function has_all_required_data($blog) {
            return !empty($blog['blog_name']) && 
                   !empty($blog['title']) && 
                   !empty($blog['link']) && 
                   !empty($blog['published_date']) && 
                   !empty($blog['excerpt']);
        }
    
        // Sorting: Prioritize complete blogs, then sort by published date
        usort($blogs, function ($a, $b) {
            $a_complete = has_all_required_data($a);
            $b_complete = has_all_required_data($b);
    
            if ($a_complete !== $b_complete) {
                return $b_complete - $a_complete; // Complete blogs come first
            }
    
            return strtotime($b['published_date']) - strtotime($a['published_date']);
        });
    
        return array_slice($blogs, 0, 10);
    }

    public static function get_yahoo_news() {
        $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/yahoo-articles/");
    
        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }
    
        $data = wp_remote_retrieve_body($response);
        $articles = json_decode($data, true);
    
        $uniqueTitles = [];
        $articles = array_filter($articles, function ($item) use (&$uniqueTitles) {
            if (!in_array($item['title'], $uniqueTitles)) {
                $uniqueTitles[] = $item['title'];
                return true;
            }
            return false;
        });
    
        usort($articles, function ($a, $b) {
            return strtotime($b['published_date']) - strtotime($a['published_date']);
        });
    
        return array_slice($articles, 0, 10);
    }

    public static function get_substacks() {
        $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/substack-articles/", array('timeout' => 5000));
        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }
    
        $data = wp_remote_retrieve_body($response);
        $substackPosts = json_decode($data, true);
    
        // Function to check if a post has all required fields
        function has_all_required_substack_data($post) {
            return !empty($post['source_name']) &&
                !empty($post['title']) &&
                !empty($post['url']) &&
                !empty($post['subtitle']) && 
                !empty($post['cover_image']);
        }
    
        // Store only the latest article from each source_name
        $filteredPosts = [];
        foreach ($substackPosts as $post) {
            $source = $post['source_name'];
            if (!isset($filteredPosts[$source])) {
                $filteredPosts[$source] = $post;
            }
        }
    
        $finalPosts = array_values($filteredPosts);

        // Use current time as we are getting null in API
        $finalPosts = array_map(function ($post) {
            $post['published_date'] = current_time('F j, Y');
            return $post;
        }, $finalPosts);
        
        return array_slice($finalPosts, 0, 12);
    }

    public static function get_yt_videos() {
        $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/youtube-videos/", array('timeout' => 5000));

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return [];
        }
    
        $data = wp_remote_retrieve_body($response);
        $youtube_videos = json_decode($data, true);
    
        // Function to check if a post has all required fields
        function has_all_required_video_data($post) {
            return !empty($post['title']) &&
                !empty($post['channel_name']) &&
                !empty($post['description']) &&
                !empty($post['video_id']) &&
                !empty($post['published_date']);
        }
    
        usort($youtube_videos, function ($a, $b) {
            $a_complete = has_all_required_news_data($a);
            $b_complete = has_all_required_news_data($b);

            if ($a_complete !== $b_complete) {
                return $b_complete - $a_complete; // Complete blogs come first
            }

            return strtotime($b['published_date']) - strtotime($a['published_date']);
        });
    
        return $youtube_videos;
    }
}