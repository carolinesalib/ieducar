<?php

use Phinx\Migration\AbstractMigration;

class AdicionaMenuDeclaracao extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (999618, null, 21127, 'Documentos', 4, '', '_self', 1, 15, 19);");
        $this->execute("UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999618 WHERE cod_menu = 999103;");
        $this->execute("UPDATE pmicontrolesis.menu SET tt_menu = 'Declaração' WHERE cod_menu = 999103;");
    }
}
