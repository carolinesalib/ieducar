<?php

use Phinx\Migration\AbstractMigration;

class AjustaFuncaoFrequencia extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE OR REPLACE FUNCTION modules.frequencia_da_matricula(p_matricula_id integer)
          RETURNS double precision AS
        $$
          DECLARE
          v_regra_falta integer;
          v_falta_aluno_id  integer;
          v_qtd_dias_letivos_serie integer;
          v_total_faltas integer;
          v_qtd_horas_serie integer;
          v_hora_falta FLOAT;
          begin

          /*
            regra_falta:
            1- Global
            2- Por componente
          */
          v_regra_falta:= (SELECT rg.tipo_presenca FROM modules.regra_avaliacao rg
                    INNER JOIN pmieducar.serie s ON (rg.id = s.regra_avaliacao_id)
                    INNER JOIN pmieducar.matricula m ON (s.cod_serie = m.ref_ref_cod_serie)
                    where m.cod_matricula = p_matricula_id);

            v_falta_aluno_id := ( SELECT id FROM modules.falta_aluno WHERE matricula_id = p_matricula_id ORDER BY id DESC LIMIT 1 );

          IF (v_regra_falta = 1) THEN

            v_qtd_dias_letivos_serie := (SELECT s.dias_letivos
                            FROM pmieducar.serie s
                            INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                            WHERE m.cod_matricula = p_matricula_id);

            v_total_faltas := ( SELECT SUM(quantidade) FROM falta_geral WHERE falta_aluno_id = v_falta_aluno_id);

            RETURN (((v_qtd_dias_letivos_serie - v_total_faltas) * 100 ) / v_qtd_dias_letivos_serie );

          ELSE

            v_qtd_horas_serie := ( SELECT s.carga_horaria
                            FROM pmieducar.serie s
                            INNER JOIN pmieducar.matricula m ON (m.ref_ref_cod_serie = s.cod_serie)
                            WHERE m.cod_matricula = p_matricula_id);

            v_total_faltas := ( SELECT SUM(quantidade) FROM modules.falta_componente_curricular WHERE falta_aluno_id = v_falta_aluno_id);

            v_hora_falta := (SELECT hora_falta FROM pmieducar.curso c
                      INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
                      WHERE m.cod_matricula = p_matricula_id);

            RETURN  (100 - ((v_total_faltas * (v_hora_falta*100))/v_qtd_horas_serie));

          END IF;

          end;$$
          LANGUAGE plpgsql VOLATILE
          COST 100;
        ALTER FUNCTION modules.frequencia_da_matricula(integer)
          OWNER TO postgres;");
    }
}
