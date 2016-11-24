<?php

use Phinx\Migration\AbstractMigration;

class RemoveCampoMultiseriadoCurso extends AbstractMigration
{
    public function up()
    {
        $this->query("ALTER TABLE pmieducar.curso DROP COLUMN multi_seriado;");
    }
}
