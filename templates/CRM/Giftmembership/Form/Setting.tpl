{foreach from=$elementNames key=elementName item=description}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">
      {$form.$elementName.html}
      <p class="description">{$description}</p>
    </div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
