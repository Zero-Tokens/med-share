<?php

namespace Database\Seeders;

use App\Models\DataSource;
use Illuminate\Database\Seeder;

/**
 * Fonte pública inicial: estoque BNAFAR/Hórus na API de Dados Abertos do
 * Ministério da Saúde (README, seção 6.1). Pode rodar várias vezes.
 *
 * O recurso monitorado (resource_url) é o indicado na issue #9 e ainda precisa
 * de validação: não presumir que ele representa toda a base importada.
 */
class DataSourceSeeder extends Seeder
{
    public const SLUG = 'bnafar_horus';

    public function run(): void
    {
        DataSource::query()->updateOrCreate(
            ['slug' => self::SLUG],
            [
                'name' => 'Estoque BNAFAR/Hórus (Dados Abertos do Ministério da Saúde)',
                'base_url' => 'https://apidadosabertos.saude.gov.br',
                'resource_url' => 'https://dadosabertos.saude.gov.br/dataset/assistencia_farmaceutica_sasisus_farmacia/resource/0a4a3feb-00eb-41ab-a163-c5712fec4534',
                'is_enabled' => true,
                'settings' => [
                    'endpoint' => '/daf/estoque-medicamentos-bnafar-horus',
                    'page_size' => 1000,
                ],
            ],
        );
    }
}
