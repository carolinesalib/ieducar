<?php

use Phinx\Migration\AbstractMigration;

class AdicionaTabelaTurmaSerie extends AbstractMigration
{
    public function up()
    {
        $this->execute("CREATE TABLE pmieducar.turma_serie
                        (
                          ref_cod_turma integer NOT NULL,
                          ref_cod_serie integer NOT NULL,
                          CONSTRAINT turma_serie_ref_cod_turma_fkey FOREIGN KEY (ref_cod_turma)
                              REFERENCES pmieducar.turma (cod_turma) MATCH SIMPLE
                              ON UPDATE RESTRICT ON DELETE RESTRICT,
                          CONSTRAINT turma_serie_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie)
                              REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
                              ON UPDATE RESTRICT ON DELETE RESTRICT
                        )
                        WITH (
                          OIDS=TRUE
                        );
                        ALTER TABLE pmieducar.turma_serie
                          OWNER TO ieducar;");
    }

    public function down() {
        $this->execute("DROP TABLE pmieducar.turma_serie;");
    }
}