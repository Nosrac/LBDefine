<?

function findWords($search, $maxWords = 20)
{
	$search = strtolower(trim($search));

	if (strlen($search) == 0) return [];

	exec("grep \"$search\" /usr/share/dict/words", $words);

	$scores = [];
	foreach ($words as $word)
	{
		$scores[$word] = levenshtein($search, $word);
	}

	usort($words, function($a, $b) use ($scores)
	{
		$aVal = $scores[$a];
		$bVal = $scores[$b];

		if ($aVal < $bVal) return -1;
		if ($aVal > $bVal) return 1;
		if ($aVal == $bVal) return 0;
	});

	$words = array_slice($words, 0, $maxWords);
	return $words;
}

$words = findWords( $argv[1] );

file_put_contents('/tmp/words-' . rand() . '.txt', json_encode($argv));

$suggestions = [];
foreach( $words as $word )
{
	$suggestions[] = [
		'title' => $word,
		'icon' => 'com.apple.Dictionary',
	];
}

echo json_encode($suggestions);

?>