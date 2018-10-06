@extends('shopify-auth::layouts.login')

@section('content')
<div class="Polaris-Page Polaris-Page--singleColumn">
  <div class="Polaris-Page__Content">
    <div class="Polaris-Layout__Annotation">
    <div class="Polaris-TextContainer">
      <h2 class="Polaris-Heading">Whoops!</h2>
      <div class="Polaris-Layout__AnnotationDescription">
        <p>It looks like the charge for this app was declined. You have a couple of options though:</p>
      </div>
    </div>
  </div>
    <div class="Polaris-Card">
      <div class="Polaris-ResourceList__ResourceListWrapper">
        <ul class="Polaris-ResourceList" aria-live="polite">
          <li class="Polaris-ResourceList__ItemWrapper">
            <div class="Polaris-SettingAction">
              <div class="Polaris-ResourceList-Item__Container" style="width:100%;">
                    <div class="Polaris-SettingAction__Setting">
                      <h3><b class="Polaris-TextStyle--variationStrong">Reinstall</b></h3>
                      <div>This will attempt to re-install and prompt the billing again.</div>
                    </div>
                    <div class="Polaris-SettingAction__Action"><form method="get" action="{{ route('shopify.login') }}"><input type="hidden" name="shop" value="{{ session('shopify_domain') }}" /><button type="submit" class="Polaris-Button Polaris-Button--primary"><span class="Polaris-Button__Content"><span>Reinstall</span></span></button></div>
              </div>
            </div>
          </li>
          <li class="Polaris-ResourceList__ItemWrapper">
            <div class="Polaris-SettingAction">
              <div class="Polaris-ResourceList-Item__Container" style="width:100%;">
                    <div class="Polaris-SettingAction__Setting">
                      <h3><b class="Polaris-TextStyle--variationStrong">Uninstall</b></h3>
                      <div>This will take you back to your store admin where you can completely uninstall the app.</div>
                    </div>
                    <div class="Polaris-SettingAction__Action"><button onClick="location.href='https://{{ session('shopify_domain') . '/admin/apps' }}'" type="button" class="Polaris-Button Polaris-Button--primary"><span class="Polaris-Button__Content"><span>Store Admin</span></span></button></div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection