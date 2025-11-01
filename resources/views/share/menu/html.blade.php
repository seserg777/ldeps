@php
    $normalizeLang = fn($lang) => strtolower(str_replace('_','-',$lang ?? ''));
    $langMatch = function($itemLang, $desired) use ($normalizeLang){
        $a = $normalizeLang($itemLang); $b = $normalizeLang($desired);
        if (!$a || !$b) return true; $ab = explode('-', $a)[0]; $bb = explode('-', $b)[0];
        return $a === $b || $ab === $bb;
    };
    $filter = function($items, $language) use (&$filter, $langMatch){
        return collect($items)->filter(function($i) use ($language, $langMatch){
            if (($i['menu_show'] ?? 1) == 0) return false;
            if (!empty($i['language'])) return $langMatch($i['language'], $language);
            return true;
        })->map(function($i) use (&$filter, $language){
            $i['children'] = !empty($i['children']) ? $filter($i['children'], $language) : [];
            return $i;
        })->values()->all();
    };
    $items = $filter($items ?? [], $language ?? null);
    $maxLevels = $maxLevels ?? 5; // Default to 5 levels if not provided
    $render = function($nodes, $level = 1) use (&$render, $maxLevels) {
        if (empty($nodes) || $level > $maxLevels) return '';
        $html = '<ul class="menu-level level-' . $level . '">';
        foreach ($nodes as $node) {
            $hasChildren = !empty($node['children']);
            $classes = 'menu-item level-' . $level . ($hasChildren ? ' has-children' : '');
            $html .= '<li class="' . $classes . '">'
                  .  '<a class="menu-link" href="/' . e($node['path'] ?? $node['alias']) . '.html">'
                  .  e($node['title'] ?? '') . '</a>';
            if ($hasChildren) {
                $html .= $render($node['children'], $level + 1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    };
@endphp
{!! $render($items, 1) !!}


