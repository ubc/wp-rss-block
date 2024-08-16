import { __, _n } from '@wordpress/i18n';

export const useRSSData = async( source, args ) => {

	const data = new FormData();

	data.append( 'action', 'fetch_rss' );
	data.append( 'source', JSON.stringify(source) );

	for ( let arg in args ) {
		data.append( arg, args[arg] );
	}

	const response = await fetch( ajaxurl, {
	  method: "POST",
	  credentials: 'same-origin',
	  body: data
	} );
	const responseJson = await response.json();
	
	if( responseJson.success ) {
		return responseJson.data;
	}

	return false;
}

export const isValidHttpsUrl = (string) => {
	try {
	  const newUrl = new URL(string);
	  return newUrl.protocol === 'https:';
	} catch (err) {
	  return false;
	}
}

export const resetFeedCache = async( source ) => {
	const data = new FormData();

	data.append( 'action', 'reset_rss_caches' );
	data.append( 'source', source );
	const response = await fetch( ajaxurl, {
	  method: "POST",
	  credentials: 'same-origin',
	  body: data
	} );
	const responseJson = await response.json();
	
	return responseJson.success;
}