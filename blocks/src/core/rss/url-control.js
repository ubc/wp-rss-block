import { TextControl } from '@wordpress/components';
import { useEffect } from "@wordpress/element";

import { Icon, check, close } from '@wordpress/icons';
import { isValidHttpsUrl } from './utils';

const ICON_STYLES = {
    'fill': '#fff',
    'width': '30px',
    'height': '30px',
    'borderRadius': '50%',
};

const UrlControl = props => {
    const { url, onChange } = props;


    useEffect(
        () => {
            onChange( url );
        },
        [ url ]
    );

    return (
        <>
            <div className="rss-url-control">
                <TextControl
                    label="URL"
                    value={ url }
                    onChange={ onChange }
                    className='rss-url-control__input'
                />
                { isValidHttpsUrl( url ) ? 
                    <Icon
                        icon={check}
                        style={{
                            ...ICON_STYLES,
                            'padding': '2px',
                            'background': 'green'
                        }}
                    /> :
                    <Icon
                        icon={close}
                        style={{
                            ...ICON_STYLES,
                            'padding': '5px',
                            'background': 'red'
                        }}
                    />
                }
            </div>
        </>

    );
}

export default UrlControl;