<?php

chdir(__DIR__);

const SOURCE_FILE = __DIR__ . '/src/Travelhood/OtpSimplePay/Service.php';

function parseSemantic($in)
{
    if(!preg_match('/([\d]+)\.([\d]+)\.([\d]+)/', $in, $matches)) {
        throw new RuntimeException('Invalid version format: '.$in);
    }
    return [
        'major' => intval($matches[1]),
        'minor' => intval($matches[2]),
        'patch' => intval($matches[3]),
    ];
}

function parseLevel($params)
{
    $level = 'patch';
    if(count($params)>0) {
        switch($params[0]) {
            case 'patch':
            case 'minor':
            case 'major':
                $level = $params[0];
                break;
            default:
                throw new InvalidArgumentException('Invalid parameter: '.$params[0]);
                break;
        }
    }
    return $level;
}

function getLastTag()
{
    $tags = array_filter(explode("\n", `git tag`));
    //sort($tags, SORT_NATURAL);
    usort($tags, function($a, $b) {
        $ea = explode('.', $a);
        $eb = explode('.', $b);
        $ea[0] = str_replace('v','',$ea[0]);
        $eb[0] = str_replace('v','',$eb[0]);
        if($ea[0] < $eb[0]) return -1;
        if($ea[0] > $eb[0]) return 1;
        if($ea[1] < $eb[1]) return -1;
        if($ea[1] > $eb[1]) return 1;
        if($ea[2] < $eb[2]) return -1;
        if($ea[2] > $eb[2]) return 1;
        return 0;
    });
    return parseSemantic($tags[count($tags)-1]);
}

function replaceInSource($filePath, $newVersion)
{
    $source = file_get_contents($filePath);
    $new = preg_replace('/const VERSION \= \'([^\']+)\'\;/', 'const VERSION = \'travelhood-'.$newVersion.'\';', $source, 1);
    return file_put_contents($filePath, $new);
}

$params = $argv;
array_shift($params);

$level = parseLevel($params);
echo 'Bumping ', $level, PHP_EOL;

$lastTag = getLastTag();
$newTag = $lastTag;
$newTag[$level]++;
$newVersion = 'v'.join('.', $newTag);
echo 'New tag will be ', $newVersion, PHP_EOL;

if(!replaceInSource(SOURCE_FILE, $newVersion)) {
    throw new RuntimeException('Failed to replace version in source file: '.SOURCE_FILE);
}

echo `git commit . -m "bump to ${newVersion}"`;
echo `git tag ${newVersion}`;
echo "Don't forget to push to remote!", PHP_EOL;
