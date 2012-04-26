 <div id="body">
  <h1><?php echo $event['name']; ?></h1>
  <h2>Informations</h2>
  <p><b>D&eacute;but :</b> <?php echo $event['startDate']; ?></p>
  <p><b>Fin :</b> <?php echo $event['endDate']; ?></p>
  <p><b>Places :</b> <?php echo $event['capacity']; ?></p>
  <?php if($event['fee'] != 0): ?>
  <p><b>Prix / Personne :</b> <?php echo $event['fee']; ?></p>
  <?php endif; ?>
  <div id="subscribeBox">
  <?php if($user != false && $user['id'] != $event['idOwner']): ?>
   <?php $canSubscribe = true; ?>
   <?php foreach($subscriptions as $subscription): ?>
    <?php if($subscription['idUser'] == $user['id']): ?>
     <?php $canSubscribe = false; ?>
    <?php endif; ?>
   <?php endforeach; ?>
   <?php if($canSubscribe): ?>
    <p><?php echo anchor('event/subscribe/' . $event['id'], "S'inscrire"); ?></p>
   <?php else: ?>
    <p><?php echo anchor('event/unsubscribe/' . $event['id'], "Se d&eacute;sinscrire"); ?></p>
   <?php endif; ?>
  <?php elseif($user != false && $user['id'] == $event['idOwner']) ?>
	<p><?php echo anchor('event/delete/' . $event['id'], "Supprimer l'&eacute;v&egrave;nement"); ?></p>
  <?php endif; ?>
  </div>
  <ul class="attendeeList">
   <?php $i = 0; ?>
   <?php foreach($subscriptions as $subscription): ?>
    <?php if($i == 0): ?>
     <li class="eventOwner"><?php echo $subscription['userName']; ?></li>
    <?php else: ?>
     <?php if($i >= $event['capacity']): ?>
      <li class="waitList"><?php echo $subscription['userName']; ?></li>
     <?php else: ?>
      <li><?php echo $subscription['userName']; ?></li>
     <?php endif; ?>
    <?php endif; ?>
    <?php $i++; ?>
   <?php endforeach; ?>
  </ul>
 </div>