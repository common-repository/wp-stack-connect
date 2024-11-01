exports.handler    = async( event ) => {
	const response = {
		statusCode: 200,
		body: JSON.stringify( "Store Zip file to AWS S3" )
	};
	return response;
}
