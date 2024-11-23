<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\tag\widgets
 * @category   CategoryName
 */

namespace open20\amos\tag\widgets;

use open20\amos\tag\AmosTag;
use open20\amos\tag\models\EntitysTagsMm;
use open20\amos\tag\models\Tag;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\widgets\InputWidget;
use open20\amos\admin\AmosAdmin;

/**
 * Class TagWidget
 *
 * @property \open20\amos\core\record\Record $model
 *
 * @package open20\amos\tag\widgets
 */
class TagWidget extends InputWidget
{
    /**
     * @var AmosTag $tagModule
     */
    public $tagModule = null;

    public
        $id = 'amos-tag', // @var string $id the id of the widget container div
        $containerClass = 'tag-widget', // @var string $containerClass the class of the widget container div

        $form,
        $name = 'tagValues',
        $trees = [],
        $singleFixedTreeId,
        $form_values,
        $isSearch = false,
        $hideHeader = false,
        $moduleCwh,
        $scope,
        $selectSonsOnly,
        $isFrontend = false;
    private static
        $allRoles;

    /**
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->tagModule = AmosTag::instance();

        parent::init();

        if (!isset($this->form_values)) {
            $post = Yii::$app->request->post($this->model->formName());
            if (!empty($post) && key_exists($this->name, $post)) {
                $this->form_values = $post[$this->name];
            }
        }

        $this->trees = $this->fetchTrees();

        if (is_null($this->selectSonsOnly)) {
            $tags = \Yii::$app->getModule(\open20\amos\tag\AmosTag::getModuleName());

            if ($tags) {
                $this->selectSonsOnly = $tags->selectSonsOnly;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $tagsSelected = $this->form_values ? $this->getTagsSelectedFromFormValues() : $this->getTagsSelected();
        $hideHeaderInternal = (isset(\Yii::$app->params['hideTagWidgetHeader']) ? \Yii::$app->params['hideTagWidgetHeader'] : $this->hideHeader);

        return $this->render(
            'tag',
            [
                'model' => $this->model,
                'form' => $this->form,
                'name' => $this->name,
                'trees' => $this->trees,
                'is_search' => $this->isSearch,
                'tags_selected' => $tagsSelected,
                'limit_trees' => $this->getLimitTrees(),
                'hideHeader' => $hideHeaderInternal,
                'id' => $this->id,
                'containerClass' => $this->containerClass,
                'moduleCwh' => $this->moduleCwh,
                'scope' => $this->scope,
                'selectSonsOnly' => $this->selectSonsOnly,
                'isFrontend' => $this->isFrontend,
            ]
        );
    }

    /**
     * @return array|ActiveRecord[]
     * @throws \ReflectionException
     */
    private function fetchTrees()
    {
        $refClass = new \ReflectionClass($this->model);
        $parentClass = $refClass->getParentClass()->name;

        /** @var ActiveQuery $query */
        $query = Tag::find()
            ->joinWith('tagModelsAuthItems')
            ->andWhere([
                'classname' => [get_class($this->model), $parentClass],
                'auth_item' => $this->getAllRoles()
            ]);

        if (!empty($this->singleFixedTreeId)) {
            $query->andWhere(["tag.id" => $this->singleFixedTreeId]);
        }

        return $query
            ->orderBy(['tag.nome' => SORT_DESC])
            ->all();
    }

    /**
     * @return array the roles that the user logged have
     */
    private function getAllRoles()
    {
        if (is_null(static::$allRoles)) {
            if (Yii::$app->getModule(AmosAdmin::getModuleName())->modelMap['UserProfile'] == get_class($this->model)) {
                $id = $this->model['user_id'];
            } else {
                $id = \Yii::$app->getUser()->getId();
            }

            $keysRoles = array_keys(
                \Yii::$app
                    ->getAuthManager()
                    ->getRolesByUser($id)
            );

            // i want all roles that user has
            static::$allRoles = [];
            foreach ($keysRoles as $role) {
                static::$allRoles = array_unique(
                    array_merge(
                        static::$allRoles,
                        array_keys(\Yii::$app->getAuthManager()->getChildRoles($role))));
            }
        }

        return static::$allRoles;
    }

    /**
     * Data la tabella delle mm tra record e oggetti, recupera le row
     * dell'oggetto per il model in esame
     *
     * Returns the selected tags for the passed record.
     * @return array
     */
    private function getTagsSelected()
    {
        $ret = [];

        if ($this->model->isNewRecord) {
            $attributeName = $this->name;
            $tagValues = $this->model->{$attributeName};

            if (empty($tagValues)) {
                return $ret;
            } else {
                /** @var Tag $tagModel */
                $tagModel = $this->tagModule->createModel('Tag');
                $tagTable = $tagModel::tableName();
                $tagsToSearchArray = explode(',', trim(str_replace(' ', '', $tagValues)));

                $query = new Query();
                $query->select([$tagTable . '.id', $tagTable . '.nome', $tagTable . '.root']);
                $query->from($tagTable);
                $query->andWhere([$tagTable . '.deleted_at' => null]);
                $query->andWhere([$tagTable . '.id' => $tagsToSearchArray]);

                $tags = $query->all();
            }
        } else {
            /** @var Tag $tagModel */
            $tagModel = $this->tagModule->createModel('Tag');
            $tagTable = $tagModel::tableName();

            /** @var EntitysTagsMm $entitysTagsMmModel */
            $entitysTagsMmModel = $this->tagModule->createModel('EntitysTagsMm');
            $entitysTagsMmTable = $entitysTagsMmModel::tableName();

            $query = new Query();
            $query->select([$tagTable . '.id', $tagTable . '.nome', $tagTable . '.root']);
            $query->from($entitysTagsMmTable);
            $query->innerJoin($tagTable, $tagTable . '.id = ' . $entitysTagsMmTable . '.tag_id');
            $query->andWhere([$tagTable . '.deleted_at' => null]);
            $query->andWhere([$entitysTagsMmTable . '.deleted_at' => null]);
            $query->andWhere([$entitysTagsMmTable . '.classname' => get_class($this->model)]);
            $query->andWhere([$entitysTagsMmTable . '.record_id' => $this->model->id]);

            $tags = $query->all();
        }

        foreach ($tags as $tag) {
            // Identifica l'id dell'albero
            $id_tree = $tag['root'];

            // Verifica se esiste già il riferimento per l'albero in esame e nel caso la crea
            if (!array_key_exists("tree_" . $id_tree, $ret)) {
                $ret["tree_" . $id_tree] = [];
            }

            // Aggiunge il tag nell'elenco dell'albero relativo
            $ret["tree_" . $id_tree][] = [
                "id" => $tag['id'],
                "label" => $tag['nome']
            ];
        }

        return $ret;
    }

    /**
     * @return array
     */
    private function getTagsSelectedFromFormValues()
    {
        $ret = [];

        if (isset($this->form_values)) {
            foreach ($this->form_values as $treeId => $treeSelectedTags) {
                $selectedTagIds = explode(',', $treeSelectedTags);
                if ($treeSelectedTags && !array_key_exists("tree_" . $treeId, $ret)) {
                    $ret["tree_" . $treeId] = [];
                }

                foreach ($selectedTagIds as $tagId) {
                    if (!empty($tagId)) {
                        $tagObj = $this->getTagById($tagId);
                        if (!is_null($tagObj)) {
                            if ($treeId == $tagObj->root) {
                                if (!array_key_exists("tree_" . $treeId, $ret)) {
                                    $ret["tree_" . $treeId] = [];
                                }

                                $element = [
                                    "id" => $tagObj->id,
                                    "label" => $tagObj->nome
                                ];

                                if (!in_array($element, $ret["tree_" . $treeId])) {
                                    $ret["tree_" . $treeId][] = $element;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @param int $tagId
     * @return Tag
     */
    private function getTagById($tagId)
    {
        return Tag::findOne($tagId);
    }

    /**
     * @return array
     */
    private function getLimitTrees()
    {
        $array_limit_trees = [];

        foreach ($this->trees as $tree) {
            //limite di default: nessun limite
            $limit_tree = false;

            if (!$this->isSearch) {
                //carica il nodo radice
                $root_node = $this->getTagById($tree['id']);

                //se è presente un limite impostato per questa radice allora lo usa
                if ($root_node->limit_selected_tag && is_numeric($root_node->limit_selected_tag)) {
                    $limit_tree = $root_node->limit_selected_tag;
                }
            }

            $array_limit_trees["tree_" . $tree['id']] = $limit_tree;
        }

        return $array_limit_trees;
    }

    /**
     * @return ActiveRecord[] tutte le root
     */
    private function fetchRoles()
    {
        /*         * @var ActiveQuery $query * */

        return Tag::find()
            ->joinWith('tagAuthItemsMms')
            ->andWhere(['auth_item' => array_keys(\Yii::$app->authManager->getRolesByUser(\Yii::$app->getUser()->getId()))])
            ->all();
    }

}
