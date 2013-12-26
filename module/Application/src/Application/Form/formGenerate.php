<?php


namespace Training\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\I18n\Translator;
use Zend\Validator\AbstractValidator;
use Zend\Captcha\Image as CaptchaImage;
use Zend\Form\Element\Captcha;

class formGenerate extends Form
{
    protected $inputFilter;
    protected $factorys;
    protected $inData;
    protected $key;
    protected $message;
    protected $pattern;
    protected $value;
    protected $min;
    protected $max;
    protected $element;
    protected $typeLabel;
    protected $translatorForm;
    protected $captcha;

    public function __construct($name,$classForm,$dataForm,$typeLabel = null) {
       // $this->value = array('typeInput'=>'');
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', $classForm);
        $this->inputFilter = new InputFilter();
        $this->factorys = new InputFactory();
        $this->inData = $dataForm;

        if($typeLabel){

            $this->typeLabel = $typeLabel;
        }

        foreach ($this->inData as $key => $value) {
            $this->key = $key;
            $this->value = $value;
            $this->setForm();
            $this->add($this->element);
        }
        $translator = new Translator();
        $this->translatorForm = $translator->addTranslationFile(
            'phpArray',
            __DIR__.'/../../../../../vendor/zendframework/zendframework/resources/languages/ru/Zend_Validate.php'
        );

//        print_r(__DIR__);
       // exit;
//
        AbstractValidator::setDefaultTranslator($translator);
     //   \Locale::setDefault('ru_RU');

        $this->setInputFilter($this->inputFilter);
    }

    public function __get($name) {

        if(method_exists($this, ($method = 'get_' . $name))) {

            return $this->$method();

        } else {
            return false;
        }
    }


    public function __set($name, $data) {

        if(method_exists($this, ($method = $name))) {

            foreach ($data as $key => $Value) {
                $this->$key = $Value;
            }

            $data = $this->$method();

            return $data;
        }

        return false;

    }

    protected function setForm() {
        if(isset($this->value['validators']['regex'])) {
            $this->getRegexParam();
        }
        if(isset($this->value['validators']['length'])) {
            $this->getLengthParam();
        }
        $this->getLabelParam();
        $dataValidators = $this->collectionValidators();
        $dataFilters = $this->collectionFilters();

        $this->getInput();

        $this->inputFilter->add(
            $this->factorys->createInput(
                array(
                    'name' => $this->key,
                    'required' => isset($this->value['required']) ? $this->value['required'] : false,
                    'filters'=>$dataFilters,
                    'validators' => $dataValidators
                )
            )
        );

        //echo '<pre>';
            //print_r(array(
                        //'name' => $this->key,
                        //'required' => isset($this->value['required']) ? $this->value['required'] : false,
                        //'filters'=>$dataFilters,
                        //'validators' => $dataValidators
                    //));
            //echo '</pre>';
            //exit;
    }

    protected function collectionValidators()
    {
        $validate = array(
            'validators' => array(
                'email' => array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'message' => 'Вы ввели некорректный адрес E-mail.',
                        'encoding' => 'UTF-8',
                        'useDomainCheck'=>false
                    ),
                ),
               'length' => array(
                    'name' => 'StringLength',
                    'options' => array(
                        'message' => 'Минимальное количество символов %min%',
                        'encoding' => 'UTF-8',
                        'min' => $this->min,
                        'max' => $this->max,
                    ),
                ),
                'Identical' => array(
                    'name' => 'Identical',
                    'options' => array(
                        'message' => 'Пароли не совпадают.',
                        'token' => isset($this->value['validators']['Identical'])?$this->value['validators']['Identical']:"",
                    ),
                ),
                'regex' => array(
                    'name' => 'Regex',
                    'options' => array(
                        'message' => $this->message,
                        'pattern' => $this->pattern
                    ),
                ),
            ),
        );

        if (isset($this->value['validators'])) {
            if(is_bool($this->value['validators']) && $this->value['validators'] === false ){
                return array();
            }

            $dataValidators = array();

            foreach ($this->value['validators'] as $key => $value) {
                if (is_numeric($key)) {
                    $dataValidators[] = $validate['validators'][$value];
                } else {
                    $dataValidators[] = $validate['validators'][$key];
                }
            }

            return $dataValidators;

        } else {

            $dataValidators = array();
            $dataValidators[] = $validate['validators'][$this->key];

            return $dataValidators;
        }
    }

    protected function collectionFilters() {

        $filters = array(

               'tag'=> array( 'name' => 'StripTags' ),
               'trim'=> array( 'name' => 'StringTrim' ),
               'int'=>  array('name' => 'Int'),

        );
        if((!isset($this->value['filters']))) {

            $filtersDefault = array(

                 array( 'name' => 'StripTags' ),
                 array( 'name' => 'StringTrim' ),

            );

            return $filtersDefault;

        } else if($this->value['filters']==false) {

            return array();


        }else{

            $dataFilters = array();

            foreach ($this->value['filters'] as $key => $value) {

                $dataFilters[] = $filters[$value];

            }

            return $dataFilters;

        }


    }

    protected function getInput() {
//
        if(isset($this->value['typeInput']) && $this->value['typeInput'] == 'radio') {

            $this->getRadioParam();

        } else  if(isset($this->value['typeInput']) && $this->value['typeInput'] == 'checkbox') {

           $this->getCheckboxParam();

        } else if (isset($this->value['typeInput']) && $this->value['typeInput'] == 'textarea') {

            $this->getTextareaParam();

        }else if(isset($this->value['typeInput']) && $this->value['typeInput'] == 'captcha'){

            $this->getCaptchaParam();

        }else{

                $this->element = array(
                    'name' => $this->key,
                    'type' => isset($this->value['typeInput']) ? $this->value['typeInput'] : 'text',
                    'attributes' => array(
                        'id' => isset($this->value['id'])?$this->value['id']:'',
                        'placeholder' =>isset($this->value['placeholder'])?$this->value['placeholder']:'',
                        'autocomplete'=>"off"
                    ),
                    'options' => array(
                        'label' =>$this->label?$this->label:''
                    ),
                );


            }



    }

    protected function getRegexParam() {

        $regex = array(

            'message' => array(
                'name' => 'Разрешенные cимволы: буквы русского и латинского алфавитов',
                'isq' => 'Разрешенные cимволы: цифры',
                'skype' => 'Разрешенные cимволы: буквы латинского алфавита, цифры, знаки "-" и "_"',
                'phone' => 'Разрешенные cимволы: цифры',
                'date'=>'Разрешенные cимволы: цифры и знак "/"',
                'script'=>'Скрипты запрещены',
                'numbers_letters'=>'Разрешенные cимволы: латинского алфавитов и цифры'
            ),
            'pattern' => array(
                'phone' => '/^[0-9\-)(+ ]+$/u',
                'skype' => '/^[a-zA-Z0-9\.,\-_]+$/u',
                'name' => '/^[а-яА-ЯёЁa-zA-Z \-]+$/u',
                'isq' => '/^[0-9]+$/u',
                'date' => '/^[0-9\/: ]+$/u',
                'numbers_letters'=>'/^[a-zA-Z-0-9 \-]+$/u'

            )
        );

        if(isset($this->value['validators']['regex'])){


            if(is_array(($this->value['validators']['regex']))){

                    $this->message = $this->value['validators']['regex'][1];

                    $this->pattern =  $this->value['validators']['regex'][0];
            }else{

                $this->message =$regex['message'][ $this->value['validators']['regex']];

                $this->pattern = $regex['pattern'][ $this->value['validators']['regex']];

            }



            }else{

                $this->message = $regex['message'][$this->value['validators']['regex']];

                $this->pattern = $regex['pattern'][$this->value['validators']['regex']];

            }


//            echo  $this->value['validators']['length'];
//            exit;







    }

    protected function getLengthParam() {

        $length = array(
            'name' => array(3,20),
            'titleCourse'=>array(2,70),
            'password'=>array(6,20),
            'isq' => array( 6, 11 ),
            'skype' => array( 3, 40 ),
            'phone' => array( 6, 20 ),
            'note'=>array(2,255)
        );
//        echo ;
//        exit;
        if(isset($this->value['validators']['length'])){
//            echo 123;
//            exit;

            if(is_array(($this->value['validators']['length']))){

                $this->min = $this->value['validators']['length'][0];

                $this->max =  $this->value['validators']['length'][1];

            }else{

                $this->min = $length[$this->value['validators']['length']][0];

                $this->max =  $length[$this->value['validators']['length']][1];

            }


//            echo  $this->value['validators']['length'];
//            exit;

        }else{

             $this->min = $length[$this->key][0];
//            exit;
             $this->max = $length[$this->key][1];
        }
    }

    protected function  getRadioParam() {

        $radio = array(
            'addLesson' => array(
                '0' => 'вставить в начало списка',
                '1' => 'вставить в конец списка',
                '2' => 'вставить после урока'
            ),
            'addLessonHidden' => array(
                '0' => 'считать новым для всех. Для всех учеников этот урок считать новым и не пройденным.',
                '1' => 'внедрить незаметно. Для учеников, у которых номер текущего урока больше чем номер нового урока, считать пройденным'
            ),
            'addTraining' => array(
                '0' => 'Только после проверки ДЗ ',
                '1' => 'Сразу после отправки ДЗ на проверку',
                //'2' => 'Новое дз открывается раз в сутки, независимо от проверки'
            ),
            'sendMessage'=>array(

                'all' => 'отправить письмо всем',
                'active' => 'отправить письмо только активным пользователям',
                'noActive' => 'отправить письмо только не активным пользователям'

            ),
            'payRadio'=>array(
                '0'=>'У меня есть скретч-карта',
                '1'=>'У меня нет скретч-карты. Купить доступ',

            )
        );

        $this->element = new Element\Radio($this->key);
        $this->element->setValueOptions($radio[$this->value['typeRadio']]);
        $this->element->setValue($this->value['value']);


        //$this->add($element);

    }

    protected function getCheckboxParam() {

        $this->element = new  Element\Checkbox($this->key, array(
            'label' => $this->label,


        ));


        //$this->add($element);

    }

    protected function getTextareaParam() {
//        echo $this->value['class'];
//        exit;
        $this->element = new Element\Textarea($this->key, array(
                                        'label' =>$this->label,
                                    )
        );
        if(isset($this->value['class'])){
            $this->element->setAttribute('class',$this->value['class']);
        }


    }
    protected function getCaptchaParam() {
//        echo $this->value['class'];
//        exit;
        $captchaImage = new CaptchaImage(array(
                'font' =>'./public/capcha/Times New Roman Cyr Regular.ttf',
                'width' => 250,
                'height' => 100,
                'dotNoiseLevel' => 100,
                'lineNoiseLevel' => 6)
        );
        $captchaImage->setImgDir('./public/capcha');
        $captchaImage->setImgUrl('/capcha/');

        $this->element = array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'require'=>true,
            'options' => array(
                'label' => 'Please verify you are human',
                'captcha' => $captchaImage,
            ),
        );


    }

    protected function getLabelParam() {

        $label= array(
            'addTraining' => array(
                'name' => 'Напишите короткое Главное название'  ,
                'mailaddress' => 'Укажите E-mail адрес отправителя, от лица которого будет вестись E-mail рассылка',
                'pay_page' => 'Укажите страницу оплаты',
                'mailname' => 'Укажите имя отправителя писем, от лица которого будет вестись E-mail рассылка',
                'note' => 'Напишите длинное Поясняющее название',
                'image' => 'Загрузите картинку, которая будет символизировать тренинг',
            ),

            'addLesson' => array(
                'name' => 'Название урока',
                'additional' => 'Дополнительные материалы',
                'homework' => 'Домашнее задание',
                'example' => 'Образец хорошо выполненного ДЗ',
                'note'=>'Запись урока'
                //'2' => 'Новое дз открывается раз в сутки, независимо от проверки'
            ),
            'users'=>array(
                'email'=>'E-mail адрес',
                'username'=>'Имя',
                'surname'=>'Фамилия',
                'password'=>'Пароль',
                'confirmPassword'=>'Подтверждение пароля ',
                'isq'=>'ICQ',
                'phone'=>'Контактный телефон',
                'skype'=>'skype',
                'image'=>'Загрузите картинку, которая будет символизировать тренинг',
            ),
            'fac' => array(
                'question' => 'Вопрос',
                'answer' => 'Ответ',
                //'2' => 'Новое дз открывается раз в сутки, независимо от проверки'
            ),
            'homework'=>array(
                'report'=>'Ваш отчет'

            ),
            'HomeworkMessage'=>array(
                'message'=>'Напишите длинное Поясняющее название'

            ),
            'sendMessage'=>array(
                'name'=>'Заголовок письма ',
                'text'=>'Текст письма ',
                'is_vebinar'=>'Это анонс вебинара',
                'date_range'=>'Время проведения вебинара (МСК): ' ,

            )
        );

        if(isset($this->value['setLabel'])){

                $this->label = $this->value['setLabel'];
        }else{

                $this->label = isset($label[$this->typeLabel][$this->key])?$label[$this->typeLabel][$this->key]:'';
        }

//        echo $this->label;
//        exit;

        //$this->add($element);

    }
  public function   checkValidation($data){

        $flag  = false;
        $checkEmail = true;
        $checkName = true;
        $checkSurname = true;

        foreach($data as $key=>$value){

            switch($key){

                case'email';
                  $value = array_values(array_diff($value, array('')));
//                    print_r($value);
//                    exit;
                    for($i=0;$i<count($value);$i++){
//                        echo $i. ' 123 '.$value[$i].'<br />';

                            $checkEmail =   preg_match('/^[^\W][a-zA-Z0-9\_\.\-]+(\.[a-zA-Z0-9\_\-\.]+)*\@[a-zA-Z0-9_\-\.]+(\.[a-zA-Z0-9\_\.\-]+)*\.[a-zA-Z\-\.\_]{2,4}$/',trim($value[$i]));

                        if(!$checkEmail){

//                            echo count($value);
//                            exit;
                            break;
                        }

                       $data['email'][$i]= strip_tags(trim($value[$i]));
//                        echo  '123'.$data['email'][$i].'123';
//                        exit;
                    }
                    break;
                case'name';

                    for($i=0;$i<count($value);$i++){

                        if((empty($value[$i]))&&(!empty($data['email'][$i]))&&(empty($data['surname'][$i]))||($data['name'][$i]==' ' || $data['surname'][$i]==' ')){

                            $data['name'][$i]= 'Аноним';
                            continue;

                        }elseif((empty($value[$i]))&&(empty($data['email'][$i]))){

                            continue;

                        }else if(empty($value[$i])){

                            continue;

                        }

                        $checkName =   preg_match('/^[а-яА-ЯёЁa-zA-Z \-]+$/u',trim($value[$i]));
                        if(!$checkName){

                            break;
                        }
                        $data['name'][$i]= strip_tags(trim($value[$i]));
                    }
                    break;
                case'surname';


                    for($i=0;$i<count($value);$i++){

                        if((empty($value[$i]))&&(!empty($data['email'][$i]))|| $value[$i]==' '){
                            continue;
                        }elseif((empty($value[$i]))&&(empty($data['email'][$i]))){
                            continue;
                        }
                        $checkSurname =   preg_match('/^[а-яА-ЯёЁa-zA-Z \-]+$/u',trim($value[$i]));
                        if(!$checkSurname){

                            break;
                        }
                        $data['surname'][$i]= strip_tags(trim($value[$i]));

                    }
                    break;
            }
            if(($checkEmail)&&($checkName)&&($checkSurname)){

                $flag = true;

            }else{

                $flag = false;
                break;

            }

        }


        return array($flag,$data);


    }

    /**
     * Clear elements
     * @return void
     */
    public function clearElements()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element instanceof \Zend\Form\Element\Text) {
                $element->setValue('');
            }
        }
    }

    public function getValueByName($name)
    {
        if ($this->get($name)) {
            return $this->get($name)->getValue();
        }
        return;
    }


}
