<?php

use lib\content\Page;
use lib\inscription\Member;
use lib\inscription\Participant;
use lib\payments\Price;
use lib\activities\Activity;
use lib\activities\Schedule;
use lib\content\Message;
use lib\content\Display;

function quit() {
  header('Location: /account');
  exit();
}

if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {

  if (isset($_GET['rel']) && Member::isMember($_GET['rel']+0, $_SESSION['user']->id())) {
    
    $m = new Member($_GET['rel']+0);
    if ($m->minor())
      $r = new Member($m->responsible());
    
    // suppression membre
    if (isset($_GET['data']) && $_GET['data'] == 'delete') {
      $name = $m->name();
      if ($m->countResponsabilities() <= 0) {
        if ($m->delete(true)) {
          $_SESSION['msg'] = new Message('La préinscription de <em>'. $name .'</em> a bien été supprimée :/', 1, 'Suppression réussie !');
          quit();
        }
        else {
          $_SESSION['msg'] = new Message('Impossible de supprimer la préinscription de <em>'. $name .'</em>', -1, 'Suppression impossible :/');
          quit();
        }
      }
      else {
        $_SESSION['msg'] = new Message($m->name(). ' est responsable de mineurs.<br />Supprimez leur préinscription d’abord.', -1, 'Suppression impossible :/');
          quit();
      }
    }
    
    $pageInfos = array(
     'name' => $m->name(),
     'url' => '/preinscription'
    );
    $page = new Page($pageInfos['name'], $pageInfos['url'], array($pageInfos));
    
    
    // Activités
    $count_activites = Participant::countActivities($m->id());
    $plural_count_activities = $count_activites > 1 ? 's' : '';
    
    $activities_participant = '';
    if ($count_activites >= 1) {
      $activities_participant = '<table class="table table-striped"><thead>
        <tr>
          <th>Activité</th>
          <th>Horaire</th>
        </tr>
        </thead><tbody>
      ';
      foreach (Participant::Activities($m->id()) as $p) {
        $a = new Activity($p->activity());
        if (!$a->aggregate())
          $s = new Schedule($p->schedule());
        $horaire = isset($s) ? Display::Day($s->day()).' &rsaquo; '. $s->time_begin() .' à '. $s->time_end() . ($s->more() != null ? '&nbsp; &nbsp;('.$s->more().')' : '') : '<em>Pratique libre</em>';
        $horaire = isset($s) && $s->description() != null ? $s->description() : $horaire;
        $activities_participant .= '
          <tr>
            <td><a href="'. _FSC_ .'/activite/'. $a->url() .'" rel="external">'. $a->name() .'</a> <i class="icon-external-link" style="font-size:12px;"></i></td>
            <td>'. $horaire .'</td>
            <td><a href="#confirmBoxP'. $p->id() .'"  role="button" data-toggle="modal" style="text-decoration:none;"><i class="icon-trash"></i></a></td>
          </tr>
        ';
      }
      $activities_participant .= '</tbody></table>';
      foreach (Participant::Activities($m->id()) as $p)
        $activities_participant .= '
        <div id="confirmBoxP'. $p->id() .'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ConfirmDelParticipant'. $p->id() .'" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="ConfirmDelParticipant'. $p->id() .'">'. $m->name() .'</h3>
          </div>
          <div class="modal-body">
            <p class="text-error">Êtes-vous sûr de vouloir désinscrire ce membre de l’activité ?</p>
          </div>
          <div class="modal-footer">
            <a class="btn" data-dismiss="modal" aria-hidden="true">Annuler</a>
            <a href="/remove-activity/'. $p->id() .'" class="btn btn-danger">Confirmer</a>
          </div>
        </div>
        ';
    }

    
  }
  else {
    quit();
  }

}
else {
  quit();
}

?>