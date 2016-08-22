<?php echo $header; ?>
<div id="content">

    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <?php if (isset($error['error_warning'])) { ?>
      <div class="warning"> <?php echo $error['error_warning']; ?></div>
    <?php } ?>
    <div class="box">
      <div class="heading">
            <h1><img src="view/image/payment.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
              <a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a>
              <a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a>
            </div>
      </div>    
      <div class="content">

        <div id="htabs" class="htabs">
            <a href="#tab-api-details"><?php echo $tab_api_details; ?></a>
            <a href="#tab-general"><?php echo $tab_general; ?></a>
            <a href="#tab-status"><?php echo $tab_order_status; ?></a>
        </div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <div id="tab-api-details">
            <table class="form">
              <tr>
                <td><span class="required">*</span><?php echo $entry_payment_title; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_payment_title" value="<?php echo $payu_easyplus_payment_title; ?>" size="50"/>
                  <?php if (isset($error['error_payment_title'])) { ?>
                    <div class="text-danger"><?php echo $error['error_payment_title']; ?></div>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td><span class="required">*</span><?php echo $entry_safe_key; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_safe_key" value="<?php echo $payu_easyplus_safe_key; ?>" size="50"/>
                  <?php if (isset($error['error_safe_key'])) { ?>
                    <div class="text-danger"><?php echo $error['error_safe_key']; ?></div>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td><span class="required">*</span><?php echo $entry_api_username; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_api_username" value="<?php echo $payu_easyplus_api_username; ?>" />
                  <?php if (isset($error['error_api_username'])) { ?>
                    <div class="text-danger"><?php echo $error['error_api_username']; ?></div>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td><span class="required">*</span><?php echo $entry_api_password; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_api_password" value="<?php echo $payu_easyplus_api_password; ?>" />
                  <?php if (isset($error['error_api_password'])) { ?>
                    <div class="text-danger"><?php echo $error['error_api_password']; ?></div>
                  <?php } ?>
                </td>
              </tr>
            </table>
          </div>

          <div id="tab-general">
            <table class="form">
              <tr>
                <td><?php echo $entry_transaction_mode; ?></td>
                <td>
                  <select name="payu_easyplus_transaction_mode" id="input-transaction_mode" class="form-control">
                    <?php if ($payu_easyplus_transaction_mode == 'staging') { ?>
                      <option value="staging" selected="selected"><?php echo $text_staging; ?></option>
                    <?php } else { ?>
                      <option value="staging"><?php echo $text_staging; ?></option>
                    <?php } ?>
                    <?php if ($payu_easyplus_transaction_mode == 'production') { ?>
                      <option value="production" selected="selected"><?php echo $text_production; ?></option>
                    <?php } else { ?>
                      <option value="production"><?php echo $text_production; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_transaction_type; ?></td>
                <td>
                  <select name="payu_easyplus_transaction_type" id="input-transaction-type" class="form-control">
                    <?php if ($payu_easyplus_transaction_type == 'PAYMENT') { ?>
                      <option value="PAYMENT" selected="selected"><?php echo $text_payment; ?></option>
                    <?php } else { ?>
                      <option value="PAYMENT"><?php echo $text_payment; ?></option>
                    <?php } ?>
                    <?php if ($payu_easyplus_transaction_type == 'RESERVE') { ?>
                      <option value="RESERVE" selected="selected"><?php echo $text_reserve; ?></option>
                    <?php } else { ?>
                      <option value="RESERVE"><?php echo $text_reserve; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_payment_methods; ?></td>
                <td>
                  <select name="payu_easyplus_payment_methods[]" id="input-payment-methods" class="form-control" multiple="multiple">
                    <?php foreach ($payment_methods as $payment_method) { ?>
                      <?php if (in_array($payment_method['value'], $payu_easyplus_payment_methods)) { ?>
                        <option value="<?php echo $payment_method['value']; ?>" selected="selected"><?php echo $payment_method['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $payment_method['value']; ?>"><?php echo $payment_method['name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                  <?php if (isset($error['error_payment_methods'])) { ?>
                    <div class="text-danger"><?php echo $error['error_payment_methods']; ?></div>
                  <?php } ?>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_total; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_total" value="<?php echo $payu_easyplus_total; ?>"  />
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_payment_currency; ?></td>
                <td>
                  <select name="payu_easyplus_payment_currency" id="input-payment-currency" class="form-control">
                    <?php foreach ($supported_currencies as $currency) { ?>
                      <?php if ($currency['value'] == $payu_easyplus_payment_currency) { ?>
                        <option value="<?php echo $currency['value'] ?>" selected="selected"><?php echo $currency['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $currency['value'] ?>"><?php echo $currency['name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_debug; ?></td>
                <td>
                  <select name="payu_easyplus_debug" id="input-debug" class="form-control">
                    <?php if ($payu_easyplus_debug) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_extended_debug; ?></td>
                <td>
                  <select name="payu_easyplus_extended_debug" id="input-extended_debug" class="form-control">
                    <?php if ($payu_easyplus_extended_debug) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_sort_order; ?></td>
                <td>
                  <input type="text" name="payu_easyplus_sort_order" value="<?php echo $payu_easyplus_sort_order; ?>" />
                </td>
              </tr>
              <tr>
                <td><?php echo $entry_status; ?></td>
                <td class="col-sm-10">
                  <select name="payu_easyplus_status" id="input-status" class="form-control">
                    <?php if ($payu_easyplus_status) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            </table>
          </div>
          <div id="tab-status">
            <table class="form">
              <tr>
                <td><?php echo $entry_order_status; ?></td>
                <td>
                  <select name="payu_easyplus_order_status_id" id="input-order-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                      <?php if ($order_status['order_status_id'] == $payu_easyplus_order_status_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                      <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                      <?php } ?>
                    <?php } ?>
                  </select>
                </td>
              </tr>
            </table>
          </div>
          <div class="form-group required">
              <input type="hidden" name="payu_easyplus_return_url" value="<?php echo $payu_easyplus_return_url; ?>" />
              <input type="hidden" name="payu_easyplus_cancel_url" value="<?php echo $payu_easyplus_cancel_url; ?>" />
              <input type="hidden" name="payu_easyplus_ipn_url" value="<?php echo $payu_easyplus_ipn_url; ?>" />
          </div>
        </form>
      </div>
    </div>
</div>
<?php echo $footer; ?> 

<script type="text/javascript"><!--
    $('#htabs a').tabs();
//--></script>