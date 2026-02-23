<?php
namespace SOM;

use MapasCulturais\API;
use MapasCulturais\ApiQuery;
use MapasCulturais\App;
use MapasCulturais\Controllers;
use MapasCulturais\Entities;
use MapasCulturais\i;


// class Theme extends \Subsite\Theme {
class Theme extends \MapasCulturais\Themes\BaseV2\Theme {

    static $requiredAgentFields = [
        'emailPrivado',
        'telefone1',
        'address_level0',
        'En_Pais',
        'En_Estado',
        'En_Municipio',
    ];

    static $requiredAgent1Fields = [
        'nomeCompleto',
        'dataDeNascimento',
    ];

    static function getThemeFolder() {
        return __DIR__;
    }


    function register() {
        $app = App::i();
        $theme = $this;

        $app->hook("app.register", function (&$register) use ($theme) {
            /** @var MapasCulturais\App $this */
            $visible_opportunity_types = $theme->getVisibleOpportunityTypes();
            $visible_project_types = $theme->getVisibleProjectTypes();
            $visible_space_types = $theme->getVisibleSpaceTypes();
            
            $entity_types = &$register['entity_types'];
            
            $opportunity_types = &$entity_types[Entities\Opportunity::class];
            foreach ($opportunity_types as $type) {
                if (!in_array($type->name, $visible_opportunity_types)) {
                    unset($opportunity_types[$type->id]);
                }
            }
            
            $project_types = &$entity_types[Entities\Project::class];
            foreach ($project_types as $type) {
                if (!in_array($type->name, $visible_project_types)) {
                    unset($project_types[$type->id]);
                }
            }
            
            $space_types = &$entity_types[Entities\Space::class];
            foreach ($space_types as $type) {
                if (!in_array($type->name, $visible_space_types)) {
                    unset($space_types[$type->id]);
                }
            }
        });
    }

    function _init() {
        parent::_init();

        $self = $this;
        $app = App::i();

        $app->hook("template(<<*>>.<<*>>.head):end", function () use ($self) {
            $this->part('gtm/tag-manager-pixel--head');
        });

        $app->hook("template(<<*>>.<<*>>.body):begin", function () use ($self) {
            $this->part('gtm/tag-manager-pixel--body');
        });

        $app->hook("template(<<*>>.<<*>>.head):end", function () use ($self) {
            $this->part('analytics/head');
        });

        /* OBRIGATORIEDADE DOS CAMPOS DOS AGENTES */
        $app->hook('entity(Agent).update:before', function () use($self) {
            /** @var Entities\Agent $this */
            if ($this->type->id == 1) {
                $self->agentRequiredProperties($self::$requiredAgent1Fields);
            }

            $required_fields = $self::$requiredAgentFields;
            $country = $this->En_Pais ?: null;
            $is_brasil = ($country == 'BR') || is_null($country);
            
            // Remove Estado e Município da lista de obrigatórios se não for Brasil
            if ($country && !$is_brasil) {
                $required_fields = array_values(array_filter($required_fields, function($field) {
                    return !in_array($field, ['En_Estado', 'En_Municipio', 'En_Pais']);
                }));
            }

            // Remove address_level0 da lista de obrigatórios se for Brasil
            if($is_brasil) {
                $required_fields = array_values(array_filter($required_fields, function($field) {
                    return !in_array($field, ['address_level0']);
                }));
            }

            $self->agentRequiredProperties($required_fields);
        });


        $app->hook('GET(agent.edit):before', function () use($self) {
            /** @var Controllers\Agent $this */
            
            $agent = $this->requestedEntity;

            if ($agent->type->id == 1) {
                $self->agentRequiredProperties($self::$requiredAgent1Fields);
            }

            $required_fields = $self::$requiredAgentFields;
            $country = $agent->En_Pais ?: null;
            $is_brasil = ($country == 'BR') || is_null($country);
            
            // Remove Estado e Município da lista de obrigatórios se não for Brasil
            if ($country && !$is_brasil) {
                $required_fields = array_values(array_filter($required_fields, function($field) {
                    return !in_array($field, ['En_Estado', 'En_Municipio', 'En_Pais']);
                }));
            }

            // Remove address_level0 da lista de obrigatórios se for Brasil
            if($is_brasil) {
                $required_fields = array_values(array_filter($required_fields, function($field) {
                    return !in_array($field, ['address_level0']);
                }));
            }

            $self->agentRequiredProperties($required_fields);
        });

        /* ALTERA O TIPO DE REQUISIÇÃO DO SALVAMENTO DE AGENTES PARA PUT */
        $app->hook('view(agent.edit).updateMethod', function(&$update_method) {
            $update_method = 'PUT';
        });

        /* REMOVE A VALIDAÇÃO DA ÁREA DE ATUAÇÃO */
        $app->hook('entity(Agent).validationErrors', function (&$errors) use($self) {
            /** @var Entities\Agent $this */
            
            unset($errors['term-area']);

            if ($this->isNew()) {
                return;
            }

            $this->save(true);

            if($this->type->id == 1) {
                foreach($self::$requiredAgent1Fields as $field) {
                    if(!$this->$field && !isset($errors[$field])) {
                        $errors[$field] = [i::__('campo obrigatório')];
                    }
                }
            }

            $country = $this->En_Pais ?: null;
            $is_brasil = ($country == 'BR');
            $check_country_fields = $this->En_Pais ?: $this->address_level0 ?: null;
            foreach($self::$requiredAgentFields as $field) {
                if($check_country_fields) {
                    // Estado e Município só são obrigatórios se o país for Brasil
                    if (in_array($field, ['En_Estado', 'En_Municipio', 'En_Pais']) && !$is_brasil) {
                        continue;
                    }
    
                    // Remove address_level0 da lista de obrigatórios se for Brasil
                    if($field == 'address_level0' && $is_brasil) {
                        continue;
                    }
                }

                if(!$this->$field && !isset($errors[$field])) {
                    $errors[$field] = [i::__('campo obrigatório')];
                }
            }

            if (!$this->avatar) {
                $errors['file:avatar'] = [i::__('campo obrigatório')];
            }
        });

        /* ADICIONA A ÁREA DE ATUAÇÃO MÚSICA */
        $app->hook('entity(Agent).<<insert|save>>:before', function() {
            $terms = $this->terms ?: [];
            $terms['area'] = $terms['area'] ?? [];
            if (!in_array(i::__('Música'), $terms['area'])) {
                $terms['area'][] = i::__('Música');
                $this->terms = (array) $terms;
            }
        });

        /* RESTRINGE PERMISSÕES PARA CRIAR OPORTUNIDADES */
        $app->hook('POST(opportunity.index):before', function() use ($app) {
            /**
             * @var Controllers\Opportunity $this
             */
            if (!$app->user->is('admin')) {
                $this->errorJson(i::__('Permissão negada'), 403);
            }
        });

        /* RESTRINGE VISUALIZAÇÃO DO BOTÃO DE CRIAÇÃO DE OPORTUNIDADE */
        $app->hook('component(create-opportunity):params', function (&$component_name) use ($app) {
            if (!$app->user->is('admin')) {
                $component_name = 'mc-empty';
            }
        });

        /* FILTRA A API DE AGENTES */
        $app->hook('ApiQuery(Agent).joins', function(&$joins) use($app) {
            /** @var ApiQuery $this*/

            $request = $app->request;

            // Don't filter agents when listing users, editing profile, etc.
            $listing_agents = $request && $request->controllerId === 'agent' && $request->action === 'find';

            if(!$listing_agents) {
                return;
            }

            $alias = uniqid('tr_');

            $joins .= "
                JOIN e.user u
                JOIN u.__metadata um WITH um.key = 'som_active' AND um.value = '1'
                JOIN e.__termRelations {$alias}
                JOIN {$alias}.term funcao WITH funcao.taxonomy = 'funcao_musica' AND funcao.term IS NOT NULL
            ";
        });

        $app->hook('ApiQuery(Agent).params', function(&$params) {
            /** @var ApiQuery $this*/

            $funcoes_validas = ['artista', 'produtor'];
            $funcao = $params['@funcao'] ?? false;

            if ($funcao && in_array($funcao, $funcoes_validas)) {
                $terms = [
                    'artista' => [i::__('Artista')],
                    'produtor' => [i::__('Produção')],
                ];

                $filter = API::IN($terms[$funcao]);
                $params['term:funcao_musica'] = $filter;

                unset($params['@funcao']);
            }
        });

        /* FILTRA A API DE ESPAÇOS */
        $app->hook('ApiQuery(Space).params', function(&$params) use($app, $self) {
            /** @var ApiQuery $this*/

            $entity_types = $app->getRegisteredEntityTypes(Entities\Space::class);
            $visible_types = $self->getVisibleSpaceTypes();
            $visible_ids = [];
            
            foreach ($entity_types as $type) {
                if (in_array($type->name, $visible_types)) {
                    $visible_ids[] = $type->id;
                }
            }
            
            if (empty($params['type'])) {
                $params['type'] = API::IN($visible_ids);
            } else {
                $params['type'] = API::AND($params['type'], API::IN($visible_ids));
            }
        });

        /* FILTRA A API DE OPORTUNIDADES */
        $app->hook('ApiQuery(Opportunity).params', function(&$params) use($app, $self) {
            /** @var ApiQuery $this*/

            $entity_types = $app->getRegisteredEntityTypes(Entities\Opportunity::class);
            $visible_types = $self->getVisibleOpportunityTypes();
            $visible_ids = [];
            
            foreach ($entity_types as $type) {
                if (in_array($type->name, $visible_types)) {
                    $visible_ids[] = $type->id;
                }
            }
            
            if (empty($params['type'])) {
                $params['type'] = API::IN($visible_ids);
            } else {
                $params['type'] = API::AND($params['type'], API::IN($visible_ids));
            }
        });

        /* FILTRA A API DE PROJETOS */
        $app->hook('ApiQuery(Project).params', function(&$params) use($app, $self) {
            /** @var ApiQuery $this*/

            $entity_types = $app->getRegisteredEntityTypes(Entities\Project::class);
            $visible_types = $self->getVisibleProjectTypes();
            $visible_ids = [];
            
            foreach ($entity_types as $type) {
                if (in_array($type->name, $visible_types)) {
                    $visible_ids[] = $type->id;
                }
            }
            
            if (empty($params['type'])) {
                $params['type'] = API::IN($visible_ids);
            } else {
                $params['type'] = API::AND($params['type'], API::IN($visible_ids));
            }
        });

        /* DEFINE O METADADO som_active = 1 NO LOGIN  */
        $app->hook('auth.successful', function () use ($app) {
            if ($app->auth->isUserAuthenticated()) {
                $user = $app->user;

                if (!$user->som_active) {
                    $user->som_active = '1';
                    $user->save(true);
                }
            }
        });

        /* REDIRECIONA O AGENTE PARA A PÁGINA DO PERFIL SE O PERFIL NÃO ESTIVER COMPLETO */
        $app->hook('GET(<<*>>):before', function () use ($app) {
            $allowed_routes = [
                'agent' => 'unlock,edit',
                'lgpd' => 'accept',
                'auth' => '*'
            ];

            foreach ($allowed_routes as $controller_id => $actions) {
                if($this->id == $controller_id) {
                    if($actions == '*') {
                        return;
                    }

                    if(in_array($this->action, explode(',', $actions))) {
                        return;
                    }
                }
            }

            if ($app->auth->isUserAuthenticated()) {
                $profile = $app->user->profile;
                if ($profile->validationErrors) {
                    $app->redirect($profile->editUrl);
                }
            }
        });

        $this->assetManager->publishFolder('custom-fonts');

        $addTaxonomyToAgentSingle = function () {
        ?>
            <entity-terms :entity="entity" taxonomy="funcao_musica" classes="col-12" hide-required title="<?php i::esc_attr_e('Função na música');?>"></entity-terms>
        <?php
        };
        $app->hook('template(agent.single.single1-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);
        $app->hook('template(agent.single.single2-entity-info-taxonomie-area):before', $addTaxonomyToAgentSingle);

        /* REGISTRO O ÍCONE DO WIDGET DE COMUNIDADES */
        $app->hook('component(mc-icon).iconset', function(&$iconset) {
            $iconset['hand'] = 'ion:hand-right';
        });

        /* ================ NOVAS ROTAS =================== */
        // Cria rota para carregar os produtores
        $app->hook('GET(search.producers)', function() {
            $this->render('producers');
        });

        $app->hook('GET(search.artists)', function() {
            $this->render('artists');
        });

        /* ALTERA OS TÍTULOS DAS PÁGINAS */
        $app->hook('view.title(search.artists)', function(&$title) {
            $title = i::__('Artistas');
        });
        
        $app->hook('view.title(search.producers)', function(&$title) {
            $title = i::__('Produtores');
        });
        
        $app->hook('view.title(search.spaces)', function(&$title) {
            $title = i::__('Casas');
        });
        
        $app->hook('view.title(search.projects)', function(&$title) {
            $title = i::__('Festivais');
        });
    }

    public function agentRequiredProperties(array $required_metadata) {
        $app = App::i();

        $metadata = $app->getRegisteredMetadata('MapasCulturais\Entities\Agent');

        foreach($metadata as $meta) {
            if(in_array($meta->key, $required_metadata)) {
                $meta->is_required = true;
            }
        }
    }

    public function getVisibleSpaceTypes(): array {
        return [
            i::__('Aldeia'),
            i::__('Bar'),
            i::__('Casa Coletiva'),
            i::__('Casa de espetáculo'),
            i::__('Casa de shows'),
            i::__('Centro Comunitário'),
            i::__('Centro Cultural Privado'),
            i::__('Centro Cultural Público'),
            i::__('Centro de tradições'),
            i::__('Concha acústica'),
            i::__('Espaço para Eventos'),
            i::__('Estúdio'),
            i::__('Hostel'),
            i::__('Hotel'),
            i::__('Museu Privado'),
            i::__('Museu Público'),
            i::__('Outros Equipamentos Culturais'),
            i::__('Palco de Rua'),
            i::__('Plataforma Digital'),
            i::__('Ponto de Cultura'),
            i::__('Pousada'),
            i::__('Praça dos esportes e da cultura'),
            i::__('Sala de cinema'),
            i::__('Teatro'),
            i::__('Teatro de Arena'),
        ];
    }

    public function getVisibleOpportunityTypes(): array {
        return [
            i::__('Audição'),
            i::__('Concurso de bandas'),
            i::__('Conferência'),
            i::__('Curso'),
            i::__('Encontro'),
            i::__('Feira'),
            i::__('Festa'),
            i::__('Festa Popular'),
            i::__('Festa Religiosa'),
            i::__('Festival'),
            i::__('Gravação de Clipe'),
            i::__('Gravação Estúdio'),
            i::__('Jam'),
            i::__('Live'),
            i::__('Oficina'),
            i::__('Residência artística'),
            i::__('Roda'),
            i::__('Sarau'),
            i::__('Seminário'),
            i::__('Show'),
            i::__('Slam'),
            i::__('Turnê'),
        ];
    }

    public function getVisibleProjectTypes(): array {
        return [
            i::__('Festival'),
            i::__('Encontro'),
            i::__('Mostra'),
            i::__('Feira'),
        ];
    }
}
