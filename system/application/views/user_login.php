<div id="body">
 <?php echo form_open('user/login'); ?>
  <ul class="formList">
   <li>
    <label for="userMail">Email</label>
    <input type="text" name="userMail" value="<?php echo set_value('userMail'); ?>"/>
   </li>
   <li>
    <label for="userPwd">Mot de passe</label>
    <input type="password" name="userPwd"/>
   </li>
   <li>&nbsp;</li>
   <li>
    <label for="formSubmit">&nbsp;</label>
    <input type="submit" name="formSubmit" value="Connexion"/>
   </li>
  </ul>
  </form>
</div>