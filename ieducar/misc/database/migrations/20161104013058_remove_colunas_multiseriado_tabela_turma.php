<?php

use Phinx\Migration\AbstractMigration;

class RemoveColunasMultiseriadoTabelaTurma extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN ref_ref_cod_serie_mult;");
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN ref_ref_cod_escola_mult;");
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN multiseriada;");
    }
}
