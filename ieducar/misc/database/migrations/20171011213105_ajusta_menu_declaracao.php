<?php

use Phinx\Migration\AbstractMigration;

class AjustaMenuDeclaracao extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 21127 WHERE cod_menu = 999103;");
    }
}
