 <div id="body">
  <h1>Liste des utilisateurs</h1>
  <table>
   <tr>
    <th>Nom</th>
	<th>Inscription</th>
	<th>Gestion &eacute;v&egrave;nements</th>
	<th>Gestion utilisateurs</th>
   </tr>
   <?php foreach($users as $user): ?>
   <tr>
    <td><?php echo anchor('user/edit/' . $user['id'], $user['userName']); ?></td>
	<td><?php echo $user['canSubscribe'] ? "Oui" : "Non"; ?></td>
	<td><?php echo $user['canCreateEvents'] ? "Oui" : "Non"; ?></td>
	<td><?php echo $user['canAdminUsers'] ? "Oui" : "Non"; ?></td>
   </tr>
   <?php endforeach; ?>
  </table>
 </div>