import { useParams } from "react-router-dom"
import usePromotions from "../../hooks/usePromotions"

import Story from "../../components/Story"
import Loading from "../../components/Loading"

export default function Promotions() {
  const { branchId } = useParams()

  const { isLoading, isSuccess, isError, data } = usePromotions(branchId)

  return <div>
    {isLoading && <Loading />}

    {isSuccess && <Story stories={data} />}
  </div>
}