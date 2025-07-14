import { __ } from '@wordpress/i18n';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect, useState } from "@wordpress/element";
import { PanelBody, Button } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { 
	useBlockProps,
	InnerBlocks,
	store as blockEditorStore,
	InspectorControls,
	BlockContextProvider,
	BlockControls
} from '@wordpress/block-editor';

import { useRSSData, resetFeedCache } from './utils';
import ToolbarControl from './toolbar-controls';
import UrlControl from './url-control';

import './editor.scss';

const DEFAULT_TEMPLATE = [
    [ 'ctlt/api-rss-template', {}, [
		[ 'ctlt/api-rss-image', {} ],
		[ 'ctlt/api-rss-title', {} ],
		[ 'ctlt/api-rss-dates', {} ],
		[ 'ctlt/api-rss-excerpt', { moreText: 'READ MORE' } ],
	] ],
	[ 'ubc/api-pagination', {} ],
	[ 'ubc/api-no-results', {} ],
];

export default function Edit( props ) {
	const blockProps = useBlockProps();

	const { clientId, attributes, setAttributes } = props;
	const { source, instanceId, postPerPage, offset } = attributes;

	const [feedItems, setFeedItems] = useState([]);

	const { __unstableMarkNextChangeAsNotPersistent } = useDispatch( blockEditorStore );
	const id = useInstanceId( Edit );

	useEffect( () => {
		setData();
	}, [postPerPage, offset])

	useEffect( () => {
		if ( ! Number.isFinite( instanceId ) ) {
			__unstableMarkNextChangeAsNotPersistent();
			setAttributes( { instanceId: id } );
		}
	}, [ instanceId, id ] );

	const hasInnerBlocks = useSelect(
		( select ) =>
			!! select( blockEditorStore ).getBlocks( clientId ).length,
		[ clientId ]
	);

	const setData = async() => {
		const data = await useRSSData(
			wp.hooks.applyFilters( 'ctlt-rss-block.setData', source, attributes ),
			{
				'per_page': postPerPage,
				'current_page': 1,
				'offset': offset
			}
		);
		setFeedItems( data.items );
	}

	return (
		<>
			<BlockContextProvider
				value={ {
					resources: feedItems
				} }
			>
			{ hasInnerBlocks ? (
				<div { ...blockProps }>
					<InnerBlocks
						templateLock={ false }
						renderAppender={ InnerBlocks.DefaultBlockAppender }
						templateInsertUpdatesSelection={ true }
					/>
				</div>
			) : <InnerBlocks
				templateLock={ false }
				template={ DEFAULT_TEMPLATE }
				renderAppender={ InnerBlocks.DefaultBlockAppender }
				templateInsertUpdatesSelection={ true }
			/> }
			</BlockContextProvider>
			<BlockControls>
				<ToolbarControl
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title="Settings" initialOpen={ false }>
					<UrlControl
						url={ source }
						onChange={ newSource => {
							setAttributes( { source: newSource } );
						}}
					/>
					{ wp.hooks.applyFilters( 'ctlt-rss-block.settings', '', props ) }
					<br />
					<>
						<Button
							variant="secondary"
							onClick={ () => {
								setData();
							}}
						>Update</Button>
						<Button
							style={ {
								marginLeft: '10px'
							} }
							variant="secondary"
							onClick={ async() => {
								if ( window.confirm( 'Are you sure you want to reset the cache for the current feed?' ) ) {
									await resetFeedCache( wp.hooks.applyFilters( 'ctlt-rss-block.resetCache', source, attributes ) );
									await setData();
								}
							}}
						>Reset Feed Cache</Button>
					</>
				</PanelBody>
			</InspectorControls>
		</>
	)
}
