<?php

namespace Elmasry\View;

class View
{
    public static function make($view, $params = [])
    {
        $basecontent = self::getBaseContent();
        $viewcontent = self::getViewContent($view, false, $params);
        if ($viewcontent === null) {
            $viewcontent = '';
        }
        echo str_replace('{{content}}', $viewcontent, $basecontent);
    }

    public static function makeError($view, $params = [])
    {
        http_response_code(404);
        $basecontent = self::getBaseContent();
        $viewcontent = self::getViewContent($view, true, $params);
        if ($viewcontent === null) {
            $viewcontent = '';
        }
        echo str_replace('{{content}}', $viewcontent, $basecontent);
    }

    protected static function getBaseContent()
    {
        ob_start();
        include base_path() . 'views/layout/main.php';
        return ob_get_clean();
    }

    protected static function getViewContent($view, $isError = false, $params = [])
    {
        $path = $isError ? view_path() . "errors/" : view_path();

        if (str_contains($view, '.')) {
            $views = explode('.', $view);
            foreach ($views as $v) {
                if (is_dir($path . $v)) {
                    $path = $path . $v . '/';
                }
            }
            $view = $path . end($views) . '.php';
        } else {
            $view = $path . $view . '.php';
        }

        // Extract params and make them available in view file
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        if (!file_exists($view)) {
            throw new \Exception("View file not found: " . $view);
        }

        ob_start();
        include $view;
        return ob_get_clean();
    }
}
