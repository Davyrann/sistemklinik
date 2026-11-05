<?php

test('all blade view files are completely free from banned slop patterns', function () {
    $views = glob(resource_path('views/**/*.blade.php'));
    $views = array_merge($views, glob(resource_path('views/*.blade.php')));

    // Deduplicate and filter real files
    $views = array_unique(array_filter($views, 'is_file'));

    $bannedPatterns = [
        'backdrop-blur' => 'Glassmorphism backdrop-blur is banned (R-10)',
        'animate-pulse' => 'Pulsing animations are banned (R-19, R-31)',
        '&rarr;' => 'Decorative button arrows are banned (R-08)',
        '#000000' => 'Pure black is banned, use #0F172A (DESIGN.md section 3)',
        'John Doe' => 'Generic placeholder John Doe is banned (R-23, R-38)',
        'Acme' => 'Generic company name Acme is banned (R-23)',
    ];

    $violations = [];

    foreach ($views as $viewPath) {
        $content = file_get_contents($viewPath);
        $relativePath = str_replace(resource_path('views/'), '', $viewPath);

        foreach ($bannedPatterns as $pattern => $message) {
            if (str_contains($content, $pattern)) {
                $violations[] = "{$relativePath}: {$message} [Found: '{$pattern}']";
            }
        }

        // Emoji check (Unicode range for emojis)
        if (preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $content)) {
            $violations[] = "{$relativePath}: Emoji found in UI (R-04)";
        }
    }

    expect($violations)->toBeEmpty(implode("\n", $violations));
});
