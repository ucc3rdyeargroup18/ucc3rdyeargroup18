<?php
//TODO Make sidebar dynamic - fetch content from database
?>

<div class="col-md-3 sidebar">
              
              <div class="panel">
                  <?php// include '/charity/donateButton.html'; ?>
                  <?php require_once('/config.php'); ?>
 
<!--form action="charge.php" method="post">
    <label for="amount">Donate &euro; </label>
    <input type="number" value="10" name="amount" id="amount" onchange="updateAmount()"/>
    <script>
        function updateAmount(){
            StripeCheckout.open({
                amount: document.getElementById("amount").value*100
            });
            return false;
            //document.getElementById("donateButton").setAttribute("data-amount", document.getElementById("amount").value*100);
        }    
    </script>
  <script id="donateButton" src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
          data-key="<?php echo $stripe['publishable_key']; ?>"
          data-label="Donate"
          data-image="/images/clucker.jpg"
          data-amount="5000 "data-description="One year's subscription"></script>
</form-->
<label for="amount">Donate: </label>
<div class="input-group">
    <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
    <input type="number" value="10" name="amount" id="amount" class="form-control"/>
</div>
<label for="donationMessage">Message: </label>
<div>
    <input type="text" maxlength="255" placeholder="Optional" name="donationMessage" id="donationMessage" class="form-control" />
</div><br />
<button id="sendPledgeBtn" class="btn btn-primary">Donate</button>

<script id="donateButton" src="https://checkout.stripe.com/v2/checkout.js"></script>
<script>
        $('#sendPledgeBtn').click(function(){
        $('#sendPledgeBtn').html('Waiting').addClass('disabled');
      var token = function(res){
        var $input = $('<input type=hidden name=stripeToken />').val(res.id);
        var tokenId = $input.val();
        var email = res.email;
        var donationAmount = $('#amount').val()*100;
        donationAmount = donationAmount.toFixed();
        var charityID = <?=$info['CharityID']?>;
        var message = $('#donationMessage').val();
        
        //TODO get pageID and contentID

        setTimeout(function(){
          $('#sendPledgeBtn').html('Processing Donation').addClass('disabled');
          $.ajax({
            url:'/charge.php',
            cache: false,
            data:{ email : email, token:tokenId, amount : donationAmount, charityID : charityID, message : message },
            type:'POST'
          })
          .done(function(data){
            // If Payment Success 
            if(data === 'true'){
                $('#sendPledgeBtn').html('Thank You').addClass('disabled');
                var snd = new Audio("http://soundfxnow.com/soundfx/CashRegister.mp3"); // buffers automatically when created
                snd.play();
            } else {
                $('#sendPledgeBtn').html('Error Occurred').addClass('disabled'); 
            }
          })
          .error(function(){
            $('#sendPledgeBtn').html('Error, Unable to Process Payment').addClass('disabled');
          });
        },500);

        $('form:first-child').append($input).submit();
      };

      StripeCheckout.open({
        key:         'pk_test_R7riQBeDbUwT2MKZ2t0SoHny', // Your Key
        address:     false,
        amount:      $('#amount').val()*100,
        currency:    'eur',
        name:        '<?=$info["Name"]?>',
        description: 'Donation',
        panelLabel:  'Donate',
        token:       token,
        image:       '/images/clucker.jpg',
        //TODO change to charity Logo
        allowRememberMe: false
      });
      return false;
});
</script>
              </div>
              
              </div>
</div>
