<?php

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う

// モンスター達格納用
$monsters = array();
// 性別クラス
class Sex{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}
// 抽象クラス（生き物クラス）
abstract class Creature{ //抽象クラスにすることによって、必ず継承することを忘れない
  protected $name;
  protected $hp;
  public $attackMin;
  public $attackMax;
  public function sayCry(){
    History::set($this->name.'が叫ぶ！'); //ヒストリーにセット
  }
  public function setName($str){ //引数の値を自分の名前に代入
    $this->name = $str;
  }
  public function getName(){ //名前を返す
    return $this->name;
  }
  public function setHp($num){ //hpを代入
    $this->hp = $num;
  }
  public function getHp(){ //hpを返す
    return $this->hp;
  }
  public function attack($targetObj){ //アタックメソッド攻撃相手を引数に入れる
    $attackPoint = mt_rand($this->attackMin, $this->attackMax); //ダメージポイントは生き物の最小攻撃値と最大の間でランダムに決まる
    if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5; //クリティカルは1.5倍
      $attackPoint = (int)$attackPoint; //クリティカルの小数点を消す
      History::set($this->getName().'のクリティカルヒット!!'); //ヒストリーにsetメソッド、名前のクリティカルヒット
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint); //攻撃相手のhpからダメージポイントを引いて、setHPで返却
    History::set($attackPoint.'ポイントのダメージ！'); //ヒストリーにsetメソッド、ダメージポイントのダメージ
  }
  public function recovery($targetObj){ //回復メソッド自分を引数に入れる
      global $human;
      $max = $human->getHp();
      if($_SESSION['recoveryLimit'] <= 3){
        $recoveryPoint = mt_rand(10, 100); //ダメージポイントは生き物の最小攻撃値と最大の間でランダムに決まる
        if($targetObj->getHp()+$recoveryPoint >= $max){
          $targetObj->setHp($max);
        }else{
          $targetObj->setHp($targetObj->getHp()+$recoveryPoint); //攻撃相手のhpからダメージポイントを引いて、setHPで返却
        }
        History::set($recoveryPoint.'ポイントの回復！'); //ヒストリーにsetメソッド、ダメージポイントのダメージ
        History::set('ふう');
      }else{
        History::set('しかし回復はすでに使い切ってしまった。');
      }
    }
  }
// }
// 人クラス
class Human extends Creature{ //人クラス、生き物クラスから継承
  protected $sex; //性別はこのクラスとサブクラスしか使えないようにしている
  public function __construct($name, $sex, $hp, $attackMin, $attackMax) { //継承したこれらは人クラスでも共通して使う
    $this->name = $name;
    $this->sex = $sex;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function setSex($num){ //性別を代入するメソッド、
    $this->sex = $num;
  }
  public function getSex(){ //性別を返すメソッド
    return $this->sex;
  }
  public function sayCry(){ //叫ぶメソッド
    History::set($this->name.'が叫ぶ！'); //ヒストリーにセット
    switch($this->sex){ //スイッチで性別によって叫び声を変えている
      case Sex::MAN :
        History::set('ぐはぁっ！');
        break;
      case Sex::WOMAN :
        History::set('きゃっ！');
        break;
      case Sex::OKAMA :
        History::set('もっと！♡');
        break;
    }
  }
}
// 魔法使いクラス
// class MagicHuman extends Human{
//   protected $mp;
//   public function __construct($name, $sex, $hp, $img, $attackMin, $attackMax, $mp){
//     parent::__construct($name, $sex, $hp, $img, $attackMin, $attackMax);
//     $this->mp = $mp;
//   }
//   public function setMp($num){
//     $this->mp = $num;
//   }
//   public function getMp(){ //性別を返すメソッド
//     return $this->mp;
//   }
//   public function attack($targetObj){ //アタックメソッドをオーバーライド
//     if(!mt_rand(0,2)){ //3分の1の確率で魔法攻撃
//       History::set($this->name.'の魔法攻撃!!');
//       $targetObj->setHp( $targetObj->getHp() - $this->attack);
//       History::set($this->attack.'ポイントのダメージを受けた！');
//     }else{
//       parent::attack($targetObj); //アタックメソッドを継承
//     }
//   }
// }

// 神様クラス
class God extends Creature{ //人クラス、生き物クラスから継承
  public function __construct($name, $img) { //継承したこれらは人クラスでも共通して使う
    $this->name = $name;
    $this->img = $img;
  }
  public function getName(){ //名前を返す
    return $this->name;
  }
  public function getImg(){ //写真を返すメソッド
    return $this->img;
  }
  static public function maxRecovery($targetObj) {
    global $human;
    $max = $human->hp;
    // $max = $targetObj->hp;
    History::set($targetObj->hp.'回復前のまんたん');
    // $_SESSION['human']->setHp($max);

    $targetObj->setHp($max);
    $human->setHp($max);
    var_dump(get_object_vars($targetObj));
    var_dump(get_object_vars($_SESSION['human']));
    History::set('神様が全回復してくれた！');
    History::set($human->hp.'回復後のまんたん素材');
    History::set($targetObj->hp.'回復後のまんたん');
  }
  static public function powerUp($targetObj){
    // global $human;
    // $attackMin = $human->attackMin;
    // $attackMax = $human->attackMax;
    // $attackMin = $_SESSION['human']->attackMin;
    // $attackMax = $_SESSION['human']->attackMax;
    // $attackMax = $human->attackMax;
    // global $attackMax;
    // global $attackMin;

    $targetObj->attackMax = $targetObj->attackMax + 20;
    $targetObj->attackMin = $targetObj->attackMin + 20;

    // $attackMin = $targetObj->attackMin;
    // $attackMax = $targetObj->attackMax;

    // $human->attackMin = $attackMin + 20;
    // $human->attackMax = $attackMax + 20;
    // $_SESSION['human']->attackMin = $attackMin + 20;
    // $_SESSION['human']->attackMax = $attackMax + 20;

    // $targetObj->attackMin = $attackMin + 20;
    // $targetObj->attackMax = $attackMax + 20;
    // $attackMin = $attackMin + 20;
    // $attackMax = $attackMax + 20;
    History::set($_SESSION['human']->attackMin.'の回復！');
    // History::set($human->attackMin.'の回復！');
    History::set('神様が強くしてくれた！');
    History::set('最大攻撃力と最小攻撃力が20上がった！');
    var_dump(get_object_vars($targetObj));
    var_dump(get_object_vars($_SESSION['human']));
    // var_dump(get_object_vars($human));
    // return $attackMax;
    // return array($_SESSION['human']->attackMin, $_SESSION['human']->attackMax);
    // return array($human->attackMin, $human->attackMax);
  }
  public function plusHp($targetObj){
    global $human;
    var_dump(get_object_vars($human));
    // $targetObj->getHp();
    // $max = $human->hp * 2;
    // $human->hp = $max;
    // $human->hp = $human->hp * 2;
    // $human->setHp($human->hp);
    // $max = $human->getHp() * 2;
    // $targetObj->setHp() = $targetObj->hp * 2;
    // $_SESSION['human']->getHp();
    // $max = $human->getHp() * 2;
    // $targetObj->setHp($targetObj->hp * 2);
    // $human->setHp($human->getHp() * 2);
    error_log($human->getHp());
    $human->setHp($human->hp * 2);
    error_log($human->getHp());
    // $_SESSION['human'] = $human->getHp() * 2;
    // error_log($human->hp);
    // $human->setHp($max);
    // $human->hp = $human->hp + $human->hp;
    // $targetObj->hp = $max;
    // $targetObj->hp = $targetObj->getHp() * 2;
    // $human->getHp($max);
    // $targetObj->getHp();
    // History::set($targetObj->getHp().'神様が丈夫にしてくれた！');
    // History::set($targetObj->hp.'神様が丈夫にしてくれた！');
    // $targetObj->getHp() = $max;
    // if($targetObj->getHp()+$recoveryPoint >= $max)
    //   $targetObj->setHp($max);
    // $targetObj->setHp($targetObj->getHp()+$recoveryPoint);
    History::set('神様が丈夫にしてくれた！');
    History::set('最大HPが2倍になった！');
    // History::set($max.'最大HPが2倍になった！');
    // History::set($human->hp.'の回復！素材');
    // var_dump(get_object_vars($targetObj));
    var_dump(get_object_vars($human));
    // return $human;
    // return $targetObj->hp;
    // 攻撃力は加算される、体力は加算されない、加算された攻撃力も体力加算すると初期値に戻る
    // return $human;
  }
  }

// モンスタークラス
class Monster extends Creature{ //モンスタークラスも生き物クラスを継承
  // プロパティ
  protected $img; //モンスタークラスの写真
  // コンストラクタ

  public function __construct($name, $hp, $img, $attackMin, $attackMax) { //生き物クラスからこれらを継承
    $this->name = $name;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  // ゲッター
  public function getImg(){ //写真を返すメソッド
    return $this->img;
  }
  public function sayCry(){ //叫ぶメソッド
    History::set($this->name.'が叫ぶ！');
    History::set('はうっ！');
  }
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{ //魔法を使えるモンスター、モンスタークラスを継承
  private $magicAttack; //魔法アタックは魔法モンスターしか使えないから、private
  function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack) {
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
    $this->magicAttack = $magicAttack;
  }
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  public function attack($targetObj){ //アタックメソッドをオーバーライド
    if(!mt_rand(0,1)){ //5分の1の確率で魔法攻撃
      History::set($this->name.'の魔法攻撃!!');
      $targetObj->setHp( $targetObj->getHp() - $this->magicAttack);
      History::set($this->magicAttack.'ポイントのダメージを受けた！');
    }else{
      parent::attack($targetObj); //アタックメソッドを継承
    }
  }
}
// 空が飛べるモンスタークラス
class FlyMonster extends Monster{ //魔法を使えるモンスター、モンスタークラスを継承
  // private $flyAttack;
  function __construct($name, $hp, $img, $attackMin, $attackMax) {
    parent::__construct($name, $hp, $img, $attackMin, $attackMax);
    // $this->flyattack = $attackPoint * 1.2;
  }
  public function getFlyAttack(){
    return $this->flyAttack;
  }
  public function attack($targetObj){ //アタックメソッドをオーバーライド
    if(!mt_rand(0,1)){ //3分の1の確率で空飛ぶ攻撃
      $attackPoint = mt_rand($this->attackMin, $this->attackMax);
      History::set($this->name.'の空からの体当たり攻撃!!');
      History::set($attackPoint);
      $FlyattackPoint = $attackPoint * 1.2;
      $FlyattackPoint = (int)$FlyattackPoint;
      $targetObj->setHp($targetObj->getHp() - $FlyattackPoint);
      History::set($FlyattackPoint.'ポイントのダメージを受けた！');
      $_SESSION['monster']->setHp($this->getHp() - 20);
      History::set($this->name.'は反動で20ポイントのダメージを受けた！');
    }else{
      parent::attack($targetObj); //アタックメソッドを継承
    }
  }
}
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// インスタンス生成
$human = new Human('勇者見習い', Sex::OKAMA, 500, 40, 120);
$monsters[] = new Monster( 'フランケン', 100, 'img/monster01.png', 20, 40 );
// $monsters[] = new MagicMonster( 'フランケンNEO', 300, 'img/monster02.png', 20, 60, mt_rand(80, 150) );
// $monsters[] = new Monster( 'ドラキュリー', 200, 'img/monster03.png', 30, 50 );
// $monsters[] = new MagicMonster( 'ドラキュラ男爵', 400, 'img/monster04.png', 50, 80, mt_rand(60, 120) );
// $monsters[] = new Monster( 'スカルフェイス', 150, 'img/monster05.png', 30, 60 );
// $monsters[] = new Monster( '毒ハンド', 100, 'img/monster06.png', 10, 30 );
// $monsters[] = new Monster( '泥ハンド', 120, 'img/monster07.png', 20, 30 );
// $monsters[] = new Monster( '血のハンド', 180, 'img/monster08.png', 30, 50 );
// $monsters[] = new FlyMonster( 'ドラゴン', 150, 'img/monster09.png', 80, 100 );
// $monsters[] = new FlyMonster( 'レッドドラゴン', 180, 'img/monster10.jpg', 50, 80 );
// $monsters[] = new FlyMonster( 'レッドドラゴン', 180, 'img/monster10.jpg', 50, 80 );
$monsters[] = new God('神様', 'img/god.png');

function createMonster(){
  global $monsters;
  global $human;
  $monster =  $monsters[mt_rand(0, 1)];
  History::set($monster->getName().'が現れた！');
  // if(empty($_SESSION['monster'])){
    error_log($human->getHp().'クリエイト中mae');
    $_SESSION['monster'] = $monster;
    error_log($human->getHp().'クリエイト中最後');
    // var_dump($_SESSION['monster']);
  // }
}
// function createSomething(){
//   global $monsters;
//   global $human;
//   $monster =  $monsters[mt_rand(0, 1)];
//   History::set($monster->getName().'が現れた！');
//   error_log($human->getHp().'クリエイト中');
//   if($monster instanceof God){
//     error_log($human->getHp().'クリエイト中');
//     $_SESSION['god'] = $monster;
//     error_log($human->getHp().'クリエイト中');
//   }else if($monster instanceof Monster){
//     $_SESSION['monster'] = $monster;
//   };
//   error_log($human->getHp().'クリエイト中最後');
// }
function createHuman(){
  global $human;
  $_SESSION['human'] =  $human;
}
function init(){
  History::clear();
  History::set('初期化します！');
  $_SESSION['knockDownCount'] = 0;
  createHuman();
  createMonster();
  // createSomething();
}
function gameOver(){
  $_SESSION = array();
}


//1.post送信されていた場合
if(!empty($_POST)){
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $recoveryFlg = (!empty($_POST['recovery'])) ? true : false;
  $maxRecoveryFlg = (!empty($_POST['maxRecovery'])) ? true : false;
  $powerUpFlg = (!empty($_POST['powerUp'])) ? true : false;
  $plusHpFlg = (!empty($_POST['plusHp'])) ? true : false;
  error_log('POSTされた！');

  if($startFlg){
    History::set('ゲームスタート！');
    $_SESSION = array();
    init();
  }elseif($attackFlg){
    // 攻撃するを押した場合
    error_log('攻撃POSTされた！');
    // モンスターに攻撃を与える
    History::set($_SESSION['human']->getName().'の攻撃！');
    $_SESSION['human']->attack($_SESSION['monster']);
    $_SESSION['monster']->sayCry();
    var_dump(get_object_vars($_SESSION['human']));
    error_log($human->getHp().'攻撃');
      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }else{
        // hpが0以下になったら、別のモンスターを出現させる
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した！');
          $_SESSION['monster'] = '';
          createMonster();
          // createSomething();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
  }elseif($recoveryFlg){
    error_log('回復POSTされた！');
      // 回復するを押した場合
      if(empty($_SESSION['recoveryLimit'])){
        $_SESSION['recoveryLimit'] = 0;
      }
      $_SESSION['recoveryLimit']++;
      History::set($human->getName().'の回復！');
      $_SESSION['human']->recovery($_SESSION['human']);

    }elseif($maxRecoveryFlg){
      error_log('全回復POSTされた！');
      // 回復するを押した場合
      $_SESSION['monster']->maxRecovery($_SESSION['human']);
      $_SESSION['monster'] = '';
      // return $_SESSION['human'];
      createMonster();

    }elseif($powerUpFlg){//パワーアップ
      error_log('パワーPOSTされた！');
      $_SESSION['monster']->powerUp($_SESSION['human']);
      // god::powerUp($human);
      // $_SESSION['monster'] = '';//セッションをからにする、入っているとまた次もコマンドがおかしいことになるから
      // $human->attackMax;
      // History::set($human->attackMin.'の回復！');
      History::set($_SESSION['human']->attackMin.'の回復！');
      createMonster();
        // return $_SESSION['human']->attackMin;

      }elseif($plusHpFlg){
        error_log('体力2倍POSTされた！');
        // $_SESSION['god']::plusHp($_SESSION['human']);
        // $_SESSION['god']->plusHp($human);
        $_SESSION['monster']->plusHp($human);
        error_log($human->getHp().'体力にばいあと');
        // $_SESSION['god'] = '';
        // return $_SESSION['human'];
        error_log($human->getHp().'クリエイト');
        createMonster();
        // createSomething();
        // return $human;
  }else{ //逃げるを押した場合
        History::set('逃げた！');
        createMonster();
        // createSomething();
      }
      $_POST = array();
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <style>
      body{
        margin: 0 auto;
        padding: 150px;
        width: 25%;
        background: #fbfbfa;
        color: white;
      }
      h1{ color: white; font-size: 20px; text-align: center;}
      h2{ color: white; font-size: 16px; text-align: center;}
      form{
        overflow: hidden;
      }
      input[type="text"]{
        color: #545454;
        height: 60px;
        width: 100%;
        padding: 5px 10px;
        font-size: 16px;
        display: block;
        margin-bottom: 10px;
        box-sizing: border-box;
      }
      input[type="password"]{
        color: #545454;
        height: 60px;
        width: 100%;
        padding: 5px 10px;
        font-size: 16px;
        display: block;
        margin-bottom: 10px;
        box-sizing: border-box;
      }
      input[type="submit"]{
        border: none;
        padding: 15px 30px;
        margin-bottom: 15px;
        background: black;
        color: white;
        float: right;
      }
      input[type="submit"]:hover{
        background: #3d3938;
        cursor: pointer;
      }
      a{
        color: #545454;
        display: block;
      }
      a:hover{
        text-decoration: none;
      }
    </style>
  </head>
  <body>
  <h1 style="text-align:center; color:#333;">ゲーム「ドラ◯エ!!」</h1>
    <div style="background:black; padding:15px; position:relative;">
      <?php if(empty($_SESSION)){ ?>
        <h2 style="margin-top:60px;">GAME START ?</h2>
        <form method="post">
          <input type="submit" name="start" value="▶ゲームスタート">
        </form>
      <?php }else if($_SESSION['monster'] instanceof Monster){ ?>
        <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
        </div>
        <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
        <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <form method="post">
          <input type="submit" name="attack" value="▶攻撃する">
          <input type="submit" name="recovery" value="▶回復する">
          <input type="submit" name="escape" value="▶逃げる">
          <input type="submit" name="start" value="▶ゲームリスタート">
        </form>
        <?php }else if($_SESSION['monster'] instanceof God){?>
          <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
          <div style="height: 150px;">
            <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
          </div>
          <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
          <form method="post">
            <input type="submit" name="maxRecovery" value="▶回復してもらう">
            <input type="submit" name="powerUp" value="▶強くしてもらう">
            <input type="submit" name="plusHp" value="▶丈夫にしてもらう">
            <input type="submit" name="start" value="▶ゲームリスタート">
          </form>
      <?php } ?>
      <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>

  </body>
</html>
