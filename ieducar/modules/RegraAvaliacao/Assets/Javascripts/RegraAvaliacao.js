$aprovaNotaExame = document.getElementById('aprovaNotaExame');

$aprovaNotaExame.onclick = function() {
	if ($aprovaNotaExame.checked)
		$aprovaNotaExame.value = 1;
	else
		$aprovaNotaExame.value = 0;
}