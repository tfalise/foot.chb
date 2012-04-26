  <div id="body">
   <h1>Modifier les permissions d'un utilisateur</h1>
   <p>Vous pouvez ici modifier les permissions de <?php echo $user['userName']; ?> :</p>
   <?php echo form_open('user/save/' . $user['id'])); ?>
    <ul class="formList">
     <li>
	  <input type="checkbox" id="canSubscribe" name="canSubscribe" <?php echo $user['canSubscribe'] ? "checked=\"checked\" " : ""; ?>/>
      <label for="canSubscribe">L'utilisateur peut s'inscrire aux &eacute;v&egrave;nements</label>
     </li>
     <li>
      <input type="checkbox" id="canCreateEvents" name="canCreateEvents" <?php echo $user['canCreateEvents'] ? "checked=\"checked\" " : ""; ?>/>
      <label for="canCreateEvents">L'utilisateur peut cr&eacute;er des &eacute;v&egrave;nements</label>
     </li>
     <li>
      <input type="checkbox" id="canAdminUsers" name="canAdminUsers" <?php echo $user['canAdminUsers'] ? "checked=\"checked\" " : ""; ?>/>
      <label for="canAdminUsers">L'utilisateur peut g&eacute;rer les permissions des utilisateurs</label>
     </li>
     <li>&nbsp;</li>
     <li>
      <label for="formSubmit">&nbsp;</label>
      <input type="submit" name="formSubmit" value="Enregistrer"/>
     </li>
    </ul>
   </form>
  </div>
  