import React from "react";
import { isNil, isEmpty, pick, find, get } from "lodash";

function FiltersLine({ meta }) {
	const selectFiltersByMeta = meta => {
		if (isNil(meta) || isEmpty(meta)) {
			return [];
		}

		return pick(
			IMAGESEO_DATA.CONDITIONS_FILTERS,
			get(find(IMAGESEO_DATA.METAS_FILTERS, meta), "conditions", [])
		);
	};

	const filters = selectFiltersByMeta(meta);
	return (
		<>
			<select>
				<option>-</option>
				{IMAGESEO_DATA.METAS_FILTERS.map(metaOption => {
					return (
						<option key={metaOption.id} value={metaOption.id}>
							{metaOption.name}
						</option>
					);
				})}
			</select>
			<select>
				<option value="NONE">-</option>
				{filters.map(filterOption => {
					return (
						<option key={filterOption.id} value={filterOption.id}>
							{filterOption.name}
						</option>
					);
				})}
			</select>
		</>
	);
}

export default FiltersLine;
