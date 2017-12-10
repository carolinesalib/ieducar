<?php

use Phinx\Migration\AbstractMigration;

class AddCampoAprovaNotaExame extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE modules.regra_avaliacao ADD COLUMN aprova_nota_exame INTEGER DEFAULT 0;');
    }
}
