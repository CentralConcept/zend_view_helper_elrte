<?php
class Zend_View_Helper_Elrte extends Zend_View_Helper_Abstract
{
    //@see http://elrte.org/redmine/projects/elrte/repository/revisions/a3abae1537eb5ff4b733e19580e1b6eebbad1599/entry/src/elrte/js/elRTE.options.js
    protected $_params = array(
        'toolbar'       => 'tiny',  //(tiny|compact|normal|complete|maxi) (bloger)
        'more_buttons'  => array(), //список дополнительных кнопочек
        'height'        => 200,     //px
        'lang'          => 'ru',    //px
        'styleWithCss'  => 'false',
        'css'           => array(), //array of css files
        'allowSource'   => 'false',
        'fmAllow'       => 'true', //file manager
        'elrte_path'    => '/js/elrte/1.0.1',
        'ui_path'       => '/js/jquery-ui/1.8.0',
        'objectId'      => 0 //id of edited object
    );


    /**
     * Подключение редактора для поля textarea по его id
     *
     * @see http://elrte.ru/
     * 
     * @param str   $css_id id of object
     * @param array $params @see $this->_params
     *
     * @todo 
     *
     * @return подключаем редактор к нужному обЪекту(по $css_id)
     */
    public function elrte($css_id, $params)
    {
        $this->_params = array_merge($this->_params, $params);

        $this->view->headLink()
                ->prependStylesheet($this->_params['ui_path'] . '/css/ui-lightness/jquery-ui-1.8.custom.css')
                ->prependStylesheet($this->_params['elrte_path']    . '/css/elrte.full.css');


        $this->view->headScript()
             ->appendFile($this->_params['ui_path']    . '/js/jquery-ui-1.8.custom.min.js')
             ->appendFile($this->_params['elrte_path'] . '/js/elrte.min.js')
             ->appendFile($this->_params['elrte_path'] . '/js/i18n/elrte.ru.js');


        $this->view->headScript()->captureStart() ?>
            $().ready(function() {

                <?php echo $this->_getToolbar() ?>

                var opts = {
                        userLogin       : '<?php echo $this->view->name              ?>',
                        objectId        : <?php  echo $this->_params['objectId']      ?>,
                        lang            : '<?php echo $this->_params['lang']         ?>',
                        styleWithCss    :  <?php echo $this->_params['styleWithCss'] ?>,
                        height          :  <?php echo $this->_params['height']       ?>,
                        toolbar         : ['<?php echo $this->_params['toolbar']     ?>'],
                        allowSource     :  <?php echo $this->_params['allowSource']  ?>,
                        cssfiles        : [
                                    '<?php echo $this->_params['elrte_path'] ?>/css/elrte-inner.css'
                                     <?php if(count($this->_params['css'])) echo ','. implode(',',$this->_params['css']); ?>
                                        ],
                        fmAllow         :  <?php echo $this->_params['fmAllow'] ?>
                        <?php if($this->_params['fmAllow'] == 'true'):?>
                            ,
                            fmOpen : function(callback) {
                                $('<div />').elfinder({
                                    url : 'connector/connector.php',
                                    lang : 'en',
                                    dialog : { width : 900, modal : true },
                                    editorCallback : callback
                                });
                            }
                        <?php endif; ?>
                    };
                $("#<?php echo $css_id ?>").elrte(opts);
            });
        <?php $this->view->headScript()->captureEnd();

        return true;
    }


    /**
     * формируем свои тулбары
     * 
     * @toolbars:
     *  - bloger - тулбар для редактора блогов (blog.joylife.ru)
     *  ...
     *
     * @return include new toolbar on elrte-editor
     */
    protected function _getToolbar()
    {
        switch($this->_params['toolbar']){
            case 'bloger': //тулбар 'bloger'

                $this->view->headScript()   //подключаем доп скрипты
                     ->appendFile($this->_params['elrte_path'] . '/js/button.joyimage.js')
                     ->appendFile($this->_params['elrte_path'] . '/js/button.joytube.js');

                $result = //формируем новую панельку из кнопок
                "elRTE.prototype.options.panels.bloger
                    = ['pagebreak','joyimage','joytube'];";

                $result .= //формируем тулбар из панелек
                "elRTE.prototype.options.toolbars.bloger
                    = ['style', 'alignment', 'lists', 'links', 'bloger'];";
                break;
            default:
                return false;
        }
        return $result;
    }
}