@extends('shopify-auth::layouts.login')

@section('content')
<div class="Polaris-Page Polaris-Page--singleColumn">
	<div class="Polaris-Layout">
		<div class="Polaris-Layout__AnnotatedSection">
	   		<div class="Polaris-Layout__AnnotationWrapper">
	      		<div class="Polaris-Layout__Annotation">
	        		<div class="Polaris-TextContainer">
	          			<h2 class="Polaris-Heading">Login</h2>
	          			<div class="Polaris-Layout__AnnotationDescription">
	            			<p>Enter your Shopify store domain below to install:</p>
	            			@if (isset($errors) && $errors->any())
								<div class="Polaris-Banner Polaris-Banner--statusWarning Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner7Heading" aria-describedby="Banner7Content" style="margin-top:20px;">
									<div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorYellowDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><g fill-rule="evenodd"><circle fill="currentColor" cx="10" cy="10" r="9"></circle><path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path></g></svg></span></div>
									<div>
										<div class="Polaris-Banner__Heading" id="Banner7Heading">
											<p class="Polaris-Heading">Whoops! There were some problems:</p>
										</div>
										<div class="Polaris-Banner__Content" id="Banner7Content">
											<ul class="Polaris-List Polaris-List--typeBullet">
												@foreach ($errors->all() as $error)
												<li class="Polaris-List__Item">{{ $error }}</li>
												@endforeach
											</ul>
										</div>
									</div>
								</div>
							@endif
	          			</div>
	        		</div>
	      		</div>
      			<div class="Polaris-Layout__AnnotationContent">
        			<div class="Polaris-Card">
          				<div class="Polaris-Card__Section">
          					<form method="post">
            				<div class="Polaris-FormLayout">
              					<div class="Polaris-FormLayout__Item">
                					<div>
                  						<div class="Polaris-Labelled__LabelWrapper">
                    						<div class="Polaris-Label"><label id="TextField1Label" for="TextField1" class="Polaris-Label__Text">Store Domain</label></div>
                  						</div>
                  						<div class="Polaris-TextField"><input id="TextField1" class="Polaris-TextField__Input" aria-label="Store Domain" aria-labelledby="TextField1Label" aria-invalid="false" placeholder="example.myshopify.com" name="shop" value="">
                    						<div class="Polaris-TextField__Backdrop"></div>
                  						</div>
                					</div>
              					</div>
              					<div class="Polaris-FormLayout__Item">
                					<div>
                  						<button type="submit" class="Polaris-Button Polaris-Button--primary Polaris-Button--fullWidth"><span class="Polaris-Button__Content"><span>Install</span></span></button>
                					</div>
              					</div>
            				</div>
            				{{ csrf_field() }}
            				</form>
          				</div>
        			</div>
      			</div>
	    	</div>
	  	</div>
	</div>
</div>
@endsection