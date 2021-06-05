import * as React from "react";

function SvgNavIcon(props) {
  return (
    <svg
      width={26}
      height={21}
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      {...props}
    >
      <path
        d="M0 0h5.57v5.57H0V0zM7.34 0h17.835v5.57H7.34V0zM0 7.228h5.57v5.57H0v-5.57zM7.34 7.228h17.835v5.57H7.34v-5.57zM0 14.456h5.57v5.571H0v-5.57zM7.34 14.456h17.835v5.57H7.34v-5.57z"
        fill="#2D9CDB"
      />
      <path
        d="M12.571 0h12.604v5.57H12.57V0zM12.571 7.228h12.604v5.57H12.57v-5.57zM12.571 14.456h12.604v5.57H12.57v-5.57z"
        fill="#56CCF2"
      />
    </svg>
  );
}

export default SvgNavIcon;
