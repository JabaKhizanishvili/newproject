function IframeClose()
{
  console.log(window.parent.$('.lity-close').click().length);

  window.parent.$.prettyPhoto.close();
}
