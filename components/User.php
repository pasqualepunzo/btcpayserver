<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Users;

/**
 * ### Ruoli utente dal README
 * 
 * 1 - Webmaster           50  => Full control su applicazione
 * 3 - Administrator       40  => Full control sui dati di tutti gli esercenti
 * 4 - Senior              30  => Visualizza tutti i propri negozi/pos/invoices
 * 2 - Junior               0  => Visualizza solo il negozio/pos/invoice assegnati
 */

class User extends Component
{   /**
     * Verifica se un utente può fare qualcosa in base al level
     * del privilegio
     */
    public static function can($level): bool
    {
        $p = Users::findOne(Yii::$app->user->identity->id);
        if ($p->privilege->level >= $level) return true;
        return false;
    }


    /**
     * Restituisce il livello di privilegio di un utente
     */
    public static function privilegeLevel($id = null)
    {
        if (null === $id) $id = Yii::$app->user->identity->id;

        $p = Users::findOne($id);
        return $p->privilege->level;
    }


    // utente webmaster
    public static function isWebmaster()
    {
        $p = Users::findOne(Yii::$app->user->identity->id);
        return ($p->privilege->level === 50);
    }

    // utente amministratore 
    public static function isAdministrator()
    {
        $p = Users::findOne(Yii::$app->user->identity->id);
        return ($p->privilege->level === 40);
    }

    // utente merchant admin
    public static function isSenior()
    {
        $p = Users::findOne(Yii::$app->user->identity->id);
        return ($p->privilege->level === 30);
    }

    // utente merchant junior
    public static function isJunior()
    {
        $p = Users::findOne(Yii::$app->user->identity->id);
        return ($p->privilege->level === 0);    
    }

}
