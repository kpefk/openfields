/**
 * Shared media-library picker used by the Image and File field types.
 */

import { BaseControl, Button, Flex, FlexItem } from '@wordpress/components';
import { MediaUpload } from '@wordpress/media-utils';
import { __, sprintf } from '@wordpress/i18n';
import type { ComponentType, ReactElement } from 'react';
import type { FieldConfig } from '../types';

interface SelectedMedia {
	id: number;
}

interface MediaUploadProps {
	allowedTypes?: string[];
	value?: number;
	onSelect: ( media: SelectedMedia ) => void;
	render: ( props: { open: () => void } ) => ReactElement;
}

// The bundled MediaUpload types do not describe its props; narrow them locally.
const TypedMediaUpload =
	MediaUpload as unknown as ComponentType< MediaUploadProps >;

interface Props {
	config: FieldConfig;
	value: number | null;
	onChange: ( value: number | null ) => void;
	disabled?: boolean;
	allowedTypes: string[];
	selectLabel: string;
}

export function MediaField( {
	config,
	value,
	onChange,
	disabled,
	allowedTypes,
	selectLabel,
}: Props ) {
	const id = typeof value === 'number' && value > 0 ? value : null;

	return (
		<BaseControl
			id={ `openfields-media-${ config.key }` }
			label={ config.label }
			help={ config.instructions }
			__nextHasNoMarginBottom
		>
			<Flex justify="flex-start" align="center">
				<FlexItem>
					<TypedMediaUpload
						allowedTypes={ allowedTypes }
						value={ id ?? undefined }
						onSelect={ ( media: SelectedMedia ) =>
							onChange( media?.id ?? null )
						}
						render={ ( { open } ) => (
							<Button
								variant="secondary"
								onClick={ open }
								disabled={ disabled }
							>
								{ id
									? sprintf(
											/* translators: %d: attachment ID. */
											__(
												'Selected #%d — change',
												'openfields'
											),
											id
									  )
									: selectLabel }
							</Button>
						) }
					/>
				</FlexItem>
				{ id ? (
					<FlexItem>
						<Button
							variant="link"
							isDestructive
							onClick={ () => onChange( null ) }
							disabled={ disabled }
						>
							{ __( 'Remove', 'openfields' ) }
						</Button>
					</FlexItem>
				) : null }
			</Flex>
		</BaseControl>
	);
}
