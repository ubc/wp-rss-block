/**
 * WordPress dependencies
 */
import { 
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';

export default () => {
	const blockProps = useBlockProps.save();

	return <div { ...blockProps }>
		<InnerBlocks.Content />
	</div>;
}