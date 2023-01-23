var jsButtonobj;
function JsButton(functionName, args, button)
{
    jsButtonobj = button;
    args['callback'] = 'JsButtonDone';
    $(button).attr('disabled', 'disabled');
    runFunction(functionName, args);
}
function JsButtonDone(args)
{
    $(jsButtonobj).attr('disabled', false);
}
function runFunction(name, args)
{
    window[name](args);
}



