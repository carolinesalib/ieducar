<?php

use Phinx\Migration\AbstractMigration;

class MigrateMultiseriadoParaTabelaTurmaSerie extends AbstractMigration
{
    public function up() {
        $this->execute("INSERT INTO pmieducar.turma_serie (ref_cod_turma, ref_cod_serie)
                          (SELECT cod_turma,
                                  ref_ref_cod_serie_mult
                           FROM pmieducar.turma
                           WHERE ref_ref_cod_serie_mult IS NOT NULL);");
    }
}
