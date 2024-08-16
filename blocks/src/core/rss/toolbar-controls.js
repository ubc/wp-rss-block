/**
 * WordPress dependencies
 */
import {
	ToolbarGroup,
	Dropdown,
	ToolbarButton,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { settings } from '@wordpress/icons';

export default function ToolbarControl( {
	attributes: { postPerPage, offset },
	setAttributes
} ) {
	return (
		<>
            <ToolbarGroup>
                <Dropdown
                    contentClassName="block-library-query-toolbar__popover"
                    renderToggle={ ( { onToggle } ) => (
                        <ToolbarButton
                            icon={ settings }
                            label={ __( 'Display settings' ) }
                            onClick={ onToggle }
                        />
                    ) }
                    renderContent={ () => (
                        <>
                            <NumberControl
                                __unstableInputWidth="60px"
                                className="block-library-query-toolbar__popover-number-control"
                                label={ __( 'Items per Page' ) }
                                labelPosition="edge"
                                min={ 1 }
                                max={ 100 }
                                onChange={ ( value ) => {
                                    if (
                                        isNaN( value ) ||
                                        value < 1 ||
                                        value > 100
                                    ) {
                                        return;
                                    }
                                    setAttributes( {
                                        postPerPage: parseInt( value ),
                                    } );
                                } }
                                step="1"
                                value={ postPerPage }
                                isDragEnabled={ false }
                            />
                            <NumberControl
                                __unstableInputWidth="60px"
                                className="block-library-query-toolbar__popover-number-control"
                                label={ __( 'Offset' ) }
                                labelPosition="edge"
                                min={ 0 }
                                max={ 100 }
                                onChange={ ( value ) => {
                                    if (
                                        value < 0 ||
                                        value > 100
                                    ) {
                                        return;
                                    }
                                    setAttributes( {
                                        offset: parseInt( value ),
                                    } );
                                } }
                                step="1"
                                value={ offset }
                                isDragEnabled={ false }
                            />
                        </>
                    ) }
                />
            </ToolbarGroup>
		</>
	);
}