<label>Estado UF:</label>
    <select class="j_loadstate form-control" name="school_uf">
      <option value="" selected> Selecione o estado </option>
                                <?php
                                $readState = new Read;
                                $readState->ExeRead("app_estados", "ORDER BY estado_nome ASC");
                                foreach ($readState->getResult() as $estado):
                                    extract($estado);
                                    echo "<option value=\"{$estado_id}\" ";

                                    if (isset($School['school_uf']) && $School['school_uf'] == $estado_id):
                                        echo 'selected';
                                    endif;

                                    echo "> {$estado_uf} / {$estado_nome} </option>";
                                endforeach;
                                ?>                        
                            </select>
                    </div>                 
                    <div class="form-group col-lg-4">
                        <label>Cidade:</label>
                        <select class="j_loadcity form-control" name="school_city" >
                            <?php if (!isset($School['school_city'])): ?>
                                <option value="" selected disabled> Selecione antes um estado </option>
                                <?php
                            else:
                                $city = new Read();
                                $city->ExeRead('app_cidades', "WHERE estado_id=:uf ORDER BY cidade_nome ASC", "uf={$School['school_uf']}");

                                if ($city->getResult()):
                                    foreach ($city->getResult() as $cidade):
                                        /** Permite pegar os indices da coluna da tabela como variaveis */
                                        extract($cidade);
                                        echo "<option value=\"{$cidade_id}\" ";

                                        if (isset($School['school_city']) && $School['school_city'] == $cidade_id):
                                            echo 'selected';
                                        endif;

                                        echo ">{$cidade_nome}</option>";
                                    endforeach;
                                endif;
                            endif;
                            ?>
                        </select>                          