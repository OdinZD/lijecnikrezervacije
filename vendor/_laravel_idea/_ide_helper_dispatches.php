<?php //772b66aaeb2d6c36cdf8bcd5bf683da45c68334ceb438eb4ea66a88e469c386b
/** @noinspection all */

namespace Illuminate\Broadcasting {

    /**
     * @method static void dispatch(array|Channel|string $channels)
     * @method static PendingBroadcast broadcast(array|Channel|string $channels)
     */
    class AnonymousEvent {}
}

namespace Illuminate\Bus {

    use Illuminate\Foundation\Bus\PendingDispatch;
    use Illuminate\Support\Fluent;

    /**
     * @method static PendingDispatch dispatch(PendingBatch $batch)
     * @method static PendingDispatch|Fluent dispatchIf(boolean $bool, PendingBatch $batch)
     * @method static PendingDispatch|Fluent dispatchUnless(boolean $bool, PendingBatch $batch)
     * @method static mixed dispatchSync(PendingBatch $batch)
     * @method static void dispatchAfterResponse(PendingBatch $batch)
     */
    class ChainedBatch {}
}

namespace Illuminate\Foundation\Console {

    use Illuminate\Foundation\Bus\PendingDispatch;
    use Illuminate\Support\Fluent;

    /**
     * @method static PendingDispatch dispatch(array $data)
     * @method static PendingDispatch|Fluent dispatchIf(boolean $bool, array $data)
     * @method static PendingDispatch|Fluent dispatchUnless(boolean $bool, array $data)
     * @method static mixed dispatchSync(array $data)
     * @method static void dispatchAfterResponse(array $data)
     */
    class QueuedCommand {}
}

namespace Illuminate\Foundation\Events {

    use Illuminate\Broadcasting\PendingBroadcast;

    /**
     * @method static void dispatch(array $stubs)
     * @method static PendingBroadcast broadcast(array $stubs)
     */
    class PublishingStubs {}
}

namespace Illuminate\Queue {

    use Illuminate\Foundation\Bus\PendingDispatch;
    use Illuminate\Support\Fluent;
    use Laravel\SerializableClosure\SerializableClosure;

    /**
     * @method static PendingDispatch dispatch(SerializableClosure $closure)
     * @method static PendingDispatch|Fluent dispatchIf(boolean $bool, SerializableClosure $closure)
     * @method static PendingDispatch|Fluent dispatchUnless(boolean $bool, SerializableClosure $closure)
     * @method static mixed dispatchSync(SerializableClosure $closure)
     * @method static void dispatchAfterResponse(SerializableClosure $closure)
     */
    class CallQueuedClosure {}
}
