<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCnpjInstituicao extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN cnpj NUMERIC(14,0)");
    }
}
