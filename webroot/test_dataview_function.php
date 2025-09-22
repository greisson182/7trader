<?php
require_once '../vendor/autoload.php';
require_once '../src/Helper/ControllerHelper.php';

use App\Helper\ControllerHelper;

echo "<h2>Teste da Função dataView() - Conversão de Datas</h2>";

echo "<h3>Conversão de Formato Americano para Brasileiro:</h3>";
echo "<p><strong>2025-01-06:</strong> " . ControllerHelper::dataView('2025-01-06') . "</p>";
echo "<p><strong>2024-12-25:</strong> " . ControllerHelper::dataView('2024-12-25') . "</p>";
echo "<p><strong>2023-02-14:</strong> " . ControllerHelper::dataView('2023-02-14') . "</p>";

echo "<h3>Teste com Valores Especiais:</h3>";
echo "<p><strong>String vazia:</strong> '" . ControllerHelper::dataView('') . "' (deve estar vazia)</p>";
echo "<p><strong>Data já brasileira (15/01/2025):</strong> " . ControllerHelper::dataView('15/01/2025') . "</p>";
echo "<p><strong>Formato inválido (2025/01/15):</strong> " . ControllerHelper::dataView('2025/01/15') . "</p>";

echo "<h3>Teste com Datas do Banco de Dados:</h3>";
$testDates = [
    '2025-01-06',
    '2024-12-31', 
    '2023-07-15',
    '2022-02-29', // Ano bissexto
    '2021-11-01'
];

foreach ($testDates as $date) {
    echo "<p><strong>$date:</strong> " . ControllerHelper::dataView($date) . "</p>";
}

echo "<h3>Simulação do Uso no StudiesController:</h3>";
$study = ['study_date' => '2025-01-06'];
echo "<p><strong>Antes:</strong> " . $study['study_date'] . "</p>";
$study['study_date'] = ControllerHelper::dataView($study['study_date']);
echo "<p><strong>Depois:</strong> " . $study['study_date'] . "</p>";