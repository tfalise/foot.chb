  <div id="body">
   <h1>Inscription</h1>
   <p>Veuillez remplir les informations suivantes afin de cr&eacute;er votre compte :</p>
   <?php echo validation_errors('<div class="formErrors">','</div>'); ?>
   <?php echo form_open('user/register'); ?>
    <ul class="formList">
     <li>
      <label for="userMail">Adresse mail</label>
      <input type="text" name="userMail" value="<?php echo set_value('userMail'); ?>"/>
     </li>
     <li>
      <label for="userPwd">Mot de passe</label>
      <input type="password" name="userPwd"/>
     </li>
     <li>
      <label for="userPwdConfirm">Confirmer</label>
      <input type="password" name="userPwdConfirm"/>
     </li>
     <li>
      <label for="userSurname">Pr&eacute;nom</label>
      <input type="text" name="userSurname" value="<?php echo set_value('userSurname'); ?>"/>
     </li>
     <li>
      <label for="userName">Nom</label>
      <input type="text" name="userName" value="<?php echo set_value('userName'); ?>"/>
     </li>
     <li>&nbsp;</li>
     <li>
      <label for="formSubmit">&nbsp;</label>
      <input type="submit" name="formSubmit" value="S'inscrire"/>
     </li>
    </ul>
   </form>
  </div>
  