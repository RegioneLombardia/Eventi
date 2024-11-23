<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\comments\widgets
 * @category   CategoryName
 */

namespace open20\amos\comments\widgets;

use open20\amos\comments\AmosComments;
use open20\amos\comments\models\Comment;
use yii\base\Widget;
use yii\data\Pagination;

/**
 * Class CommentsWidget
 *
 * Widget to show the comments for a content.
 *
 * @package open20\amos\comments\widgets
 */
class CommentsWidget extends Widget
{
    public $layout = '<div id="comments-container">{commentSection}{commentsSection}</div>';

    /**
     * @var \open20\amos\core\record\Record $model
     */
    public $model;
    public $namespaceAssetBootstrapitalia = 'amos\planner\assets\BootstrapItaliaAsset';
    public $noAttach                      = 0;
    public $frontend                      = false;
    public $layoutInverted                = false;
    public $urlRegistrazione              = null;
    public $performance                   = false;
    public $defaultLimit                  = 10;
    public $pageSize                      = 5;
    public $useDesign                     = false;
    public $moderator                     = false;

    /**
     *
     * @var array $puglins
     */
    public $plugins = [
        /*
          "advlist autolink lists charmap print preview anchor",
          "searchreplace visualblocks code fullscreen code",
          "insertdatetime media table contextmenu paste textcolor image insertdatetime",
          "placeholder", */
        "contextmenu paste link",
    ];

    /**
     *
     * @var string $toolbar
     */
    public $toolbar   = "fullscreen | undo redo | bold italic strikethrough | link | removeformat";
    
    public $rteMobile = [
        'menubar' => true,
        'plugins' => ['autosave', 'autolink'],
        'theme' => 'mobile',
        'content_style' => 'body {background-color: white;}',
        'toolbar' => [
            'fullscreen', 'undo redo', 
            'link', 'removeformat'
        ],
    ];

    public $useRTE    = false;

    /**
     * @var array $options Options array for the widget (ie. html options)
     */
    public $options = [];

    /**
     *
     * Set of the permissionSave
     */
    public function init()
    {
        $this->initDefaultOptions();

        $module = \Yii::$app->getModule('comments');
        if (!empty($module->layoutInverted) && $module->layoutInverted == true) {
            $this->layout = '<div id="comments-container">{commentsSection}{commentSection}</div>';
        }

        if (property_exists(get_class($this->model), 'bootstrapItalia') && $this->model->bootstrapItalia == true) {
            $this->layout = '{commentsSection}{commentSection}';
        }

        parent::init();
    }

    /**
     * Set default options values.
     */
    private function initDefaultOptions()
    {
        $this->options['commentPlaceholder']      = AmosComments::t('amoscomments', 'Write a comment').'...';
        $this->options['commentReplyPlaceholder'] = AmosComments::t('amoscomments', 'Write a reply').'...';
        $this->options['commentTitle']            = AmosComments::t('amoscomments', '#COMMENT_TITLE');
        $this->options['lastCommentsTitle']       = AmosComments::t('amoscomments', 'Last comments');
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    public function run()
    {
        if(\Yii::$app->user->isGuest){
            return $this->render('comments-widget/banner-cta');
        }
        $content = preg_replace_callback("/{\\w+}/",
            function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->layout);

        return $content;
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{commentSection}':
                $module = \Yii::$app->getModule('comments');
                if (!empty($module->enableCommentOnlyWithScope) && $module->enableCommentOnlyWithScope == true) {
                    $moduleCwh = \Yii::$app->getModule('cwh');
                    if (!is_null($moduleCwh)) {
                        $scope = $moduleCwh->getCwhScope();
                        if (!isset($scope['community'])) {
                            return null;
                        }
                    }
                }
                if ($this->model->hasMethod('getCloseCommentThread') && $this->model->getCloseCommentThread()) {
                    return null;
                }
                return $this->commentSection();
            case '{commentsSection}':
                return $this->commentsSection();
            default:
                return false;
        }
    }

    /**
     * Method that render the section of the comment container.
     * @return string
     */
    public function commentSection()
    {
        if ($this->frontend == true) {
            return $this->render('frontend/comment', [
                    'widget' => $this
            ]);
        } else if ($this->useDesign === true) {
            return $this->render('design/comment', [
                    'widget' => $this
            ]);
        } else if (property_exists(get_class($this->model), 'bootstrapItalia') && $this->model->bootstrapItalia == true) {
            return $this->render('bootstrapitalia/comment', [
                    'widget' => $this
            ]);
        } else {
            return $this->render('comments-widget/comment', [
                    'widget' => $this
            ]);
        }
    }

    /**
     * Method that render the comments section where there are all the comments and comments replies.
     * @return string
     */
    public function commentsSection()
    {
        $module = \Yii::$app->getModule('comments');
        /** @var \yii\db\ActiveQuery $query */
        $query  = Comment::find()->andWhere(['context' => $this->model->className(), 'context_id' => $this->model->id])->orderBy([
            'created_at' => $module->orderDisplayComments]);

        /** @var \open20\amos\comments\models\Comment $lastComment */
        $lastComment = Comment::find()->andWhere(['context' => $this->model->className(), 'context_id' => $this->model->id])->orderBy([
                'created_at' => $module->orderDisplayComments])->limit(1)->one();

        if ($this->performance == true) {
            $query->limit($this->defaultLimit);
            $comments = $query->all();
        } else if ($module->disablePagination == true) {
            $pages    = null;
            $comments = $query->all();
        } else {
            $pages    = new Pagination(['totalCount' => $query->count()]);
            $pages->setPageSize($this->pageSize);
            $comments = $query->offset($pages->offset)->limit($pages->limit)->all();
        }
        if ($this->frontend == true) {
            return $this->render('frontend/comments',
                    [
                    'widget' => $this,
                    'pages' => $pages,
                    'comments' => $comments,
                    'lastComment' => $lastComment,
                    'no_attach' => $this->noAttach,
            ]);
        } else if ($this->useDesign == true) {
            return $this->render('design/comments',
                    [
                    'widget' => $this,
                    'pages' => $pages,
                    'comments' => $comments,
                    'lastComment' => $lastComment,
                    'no_attach' => $this->noAttach,
            ]);
        } else if (property_exists(get_class($this->model), 'bootstrapItalia') && $this->model->bootstrapItalia == true) {
            return $this->render('bootstrapitalia/comments',
                    [
                    'widget' => $this,
                    'pages' => $pages,
                    'comments' => $comments,
                    'lastComment' => $lastComment,
                    'asset' => $this->namespaceAssetBootstrapitalia,
                    'no_attach' => $this->noAttach,
            ]);
        } else {
            return $this->render('comments-widget/comments',
                    [
                    'widget' => $this,
                    'pages' => $pages,
                    'comments' => $comments,
                    'lastComment' => $lastComment,
            ]);
        }
    }
}