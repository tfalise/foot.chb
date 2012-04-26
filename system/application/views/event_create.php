  <div id="body">
   <h1>Cr&eacute;ation d'un &eacute;v&egrave;nement</h1>
   <p>Vous allez cr&eacute;er un nouvel &eacute;v&egrave;nement. Pour cela, veuillez remplir les informations suivantes :</p>
   <?php echo validation_errors('<div class="formErrors">','</div>'); ?>
   <?php echo form_open('event/create/' . date('Y/n/j', $eventDate)); ?>
    <ul class="formList">
     <li>
      <label for="eventName">Nom</label>
      <input type="text" name="eventName" value="<?php echo set_value('eventName'); ?>"/>
     </li>
     <li>
      <label for="eventStartDate">Heure de d&eacute;but (hh:mm)</label>
      <input type="text" name="eventStartDate" value="<?php echo set_value('eventStartDate'); ?>"/>
     </li>
     <li>
      <label for="eventEndDate">Heure de fin (hh:mm)</label>
      <input type="text" name="eventEndDate" value="<?php echo set_value('eventEndDate'); ?>"/>
     </li>
     <li>
      <label for="eventCapacity">Places</label>
      <input type="text" name="eventCapacity" value="<?php echo set_value('eventCapacity'); ?>"/>
     </li>
     <li>
      <label for="eventFee">Prix / Personne</label>
      <input type="text" name="eventFee" value="<?php echo set_value('eventFee'); ?>"/>
     </li>
     <li>&nbsp;</li>
     <li>
      <label for="formSubmit">&nbsp;</label>
      <input type="submit" name="formSubmit" value="Cr&eacute;er"/>
     </li>
    </ul>
   </form>
  </div>
  