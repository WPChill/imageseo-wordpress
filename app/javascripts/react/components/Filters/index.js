import React, { useContext } from "react";
import FiltersLine from "./Line";
import { FiltersContext } from "../../contexts/FiltersContext";

function Filters() {
	const { state } = useContext(FiltersContext);
	console.log(state);
	return (
		<>
			{state.map((filtersOr, keyOr) => {
				return (
					<div key={`or_${keyOr}`}>
						{filtersOr.map((filtersAnd, keyAnd) => {
							return (
								<FiltersLine
									key={`and_${keyAnd}`}
									{...filtersAnd}
									keyOr={keyOr}
									keyAnd={keyAnd}
								/>
							);
						})}
					</div>
				);
			})}
		</>
	);
}

export default Filters;
